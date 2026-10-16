<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\NumberAllocationService;
use App\Application\Services\VerificationLifecycleService;
use App\Domain\Number\Exceptions\NumberUnavailableException;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Resources\VerificationSessionResource;
use App\Models\Country;
use App\Models\NumberType;
use App\Models\Service;
use App\Models\VerificationSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public function __construct(
        protected NumberAllocationService $allocationService,
        protected VerificationLifecycleService $lifecycleService
    ) {}

    /**
     * Request a virtual number and initiate an SMS verification session.
     */
    public function requestVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_name' => ['nullable', 'string', 'max:100'],
            'number_type_id' => ['nullable', 'integer', 'exists:number_types,id'],
            'duration' => ['nullable', 'integer'],
            'state' => ['nullable', 'string', 'max:50'],
            'area_code' => ['nullable', 'string', 'max:10'],
            'carrier' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($validated['country_id']) && empty($validated['country_code'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a country.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (empty($validated['service_id']) && empty($validated['service_name'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select or enter a verification service.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();

        if (!empty($validated['country_id'])) {
            $country = Country::findOrFail($validated['country_id']);
        } else {
            $cCode = strtoupper(trim($validated['country_code']));
            $pvaClient = new \App\Infrastructure\Providers\PvaDealsClient();
            $norm = $pvaClient->normalizeAllServicesCountry($cCode);
            $cConfig = $pvaClient->getAllServicesCountryConfig($norm);
            $countryName = $cConfig['name'] ?? $cCode;
            $countryDial = $cConfig['dial_code'] ?? '+1';

            $country = Country::firstOrCreate(
                ['iso2' => $norm],
                ['name' => $countryName, 'iso3' => $norm, 'dial_code' => $countryDial, 'currency_code' => 'USD', 'is_active' => true]
            );
        }

        if (!empty($validated['service_id'])) {
            $service = Service::findOrFail($validated['service_id']);
        } else {
            $name = trim($validated['service_name']);
            $code = \Illuminate\Support\Str::slug($name);
            $service = Service::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true]
            );
        }

        $numberType = isset($validated['number_type_id'])
            ? NumberType::find($validated['number_type_id'])
            : null;

        $duration = isset($validated['duration']) ? (int) $validated['duration'] : null;
        $state = !empty($validated['state']) ? trim($validated['state']) : null;
        $areaCode = !empty($validated['area_code']) ? trim($validated['area_code']) : null;

        try {
            $session = $this->allocationService->allocateVerificationNumber(
                $user,
                $country,
                $service,
                $numberType,
                'USD',
                $duration,
                $state,
                $areaCode
            );

            return response()->json([
                'success' => true,
                'message' => 'Phone number allocated successfully. Waiting for incoming SMS verification code.',
                'data' => new VerificationSessionResource($session->load(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages'])),
            ], Response::HTTP_CREATED);

        } catch (InsufficientBalanceException $e) {
            return response()->json([
                'success' => false,
                'error' => 'INSUFFICIENT_BALANCE',
                'message' => $e->getMessage(),
                'required_action' => 'deposit_funds',
            ], Response::HTTP_PAYMENT_REQUIRED);

        } catch (NumberUnavailableException $e) {
            return response()->json([
                'success' => false,
                'error' => 'NUMBER_UNAVAILABLE',
                'message' => $e->getMessage(),
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => 'ALLOCATION_FAILED',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get live status of an active verification session (long-polling or interval polling).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $session = VerificationSession::with(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new VerificationSessionResource($session),
        ]);
    }

    /**
     * Cancel an active session and release held funds back to wallet immediately.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $session = VerificationSession::with(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (!in_array($session->status, ['active', 'pending'])) {
            return response()->json([
                'success' => false,
                'error' => 'INVALID_STATE',
                'message' => "Cannot cancel a session in status '{$session->status}'.",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $reason = $request->input('reason', 'user_requested');
        $this->lifecycleService->cancelSession($session, $reason);

        return response()->json([
            'success' => true,
            'message' => 'Session cancelled successfully. Held funds have been returned to your wallet.',
            'data' => new VerificationSessionResource($session->fresh(['phoneNumber.country', 'service', 'orderItem.order', 'smsMessages'])),
        ]);
    }
}

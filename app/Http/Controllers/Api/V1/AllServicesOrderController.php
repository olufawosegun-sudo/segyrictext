<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\OrderService;
use App\Application\Services\PricingService;
use App\Application\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Models\NumberOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AllServicesOrderController extends Controller
{
    /**
     * GET /api/v1/catalog/all-services
     * Returns countries, plans, and NGN prices for all-services rentals.
     * Cached 5 min; cache key includes config hash.
     */
    public function catalog(PricingService $pricingService): JsonResponse
    {
        $catalog = $pricingService->catalog();

        return response()->json([
            'success' => true,
            'countries' => $catalog,
        ])->header('Cache-Control', 'max-age=300, public');
    }

    /**
     * GET /api/v1/wallet
     * Returns current user's wallet balance with Cache-Control: no-store.
     */
    public function wallet(Request $request, WalletService $walletService): JsonResponse
    {
        $user = $request->user();
        $balanceKobo = $walletService->getBalanceKobo($user);
        $balanceNgn = round($balanceKobo / 100, 2);

        return response()->json([
            'success' => true,
            'balance_kobo' => $balanceKobo,
            'balance_ngn' => $balanceNgn,
            'balance_formatted' => '₦' . number_format($balanceNgn, 2),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * POST /api/v1/orders
     * Requires Idempotency-Key header, validates country and duration,
     * throttles 10/min, debits wallet, dispatches provider job, and returns 202 Accepted.
     */
    public function store(Request $request, OrderService $orderService): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key') ?? $request->header('X-Idempotency-Key');

        if (empty($idempotencyKey) || !is_string($idempotencyKey)) {
            return response()->json([
                'message' => 'Idempotency-Key header is required for safe order processing.',
                'errors' => ['Idempotency-Key' => ['Missing or empty Idempotency-Key header.']],
            ], 422);
        }

        $validated = $request->validate([
            'country' => ['required', 'string', 'max:10'],
            'duration' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $order = $orderService->create(
                user: $request->user(),
                country: $validated['country'],
                duration: isset($validated['duration']) ? (int) $validated['duration'] : null,
                idempotencyKey: trim($idempotencyKey)
            );

            return response()->json([
                'success' => true,
                'message' => 'Order accepted for processing.',
                'order' => $order->toPublicArray(),
            ], 202)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while placing your order. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /api/v1/orders/{id}
     * Returns single order status, allocated phone number, and last 20 SMS messages.
     * Authorized strictly to the authenticated user.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $order = NumberOrder::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order->toPublicArray(includeSms: true),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * GET /api/v1/orders
     * Returns the user's recent orders.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = NumberOrder::where('user_id', $request->user()->id)
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn(NumberOrder $o) => $o->toPublicArray());

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}

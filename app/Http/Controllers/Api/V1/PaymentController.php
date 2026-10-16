<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\PaymentService;
use App\Application\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Infrastructure\Payments\PaymentGatewayFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected PaymentGatewayFactory $gatewayFactory,
        protected WalletService $walletService
    ) {}

    /**
     * Get available payment gateways and supported payment methods.
     */
    public function methods(Request $request): JsonResponse
    {
        $wallet = $this->walletService->getOrCreateUserWallet($request->user());
        $userCurrency = $wallet->currency?->code ?? 'USD';

        $gateways = $this->gatewayFactory->getAvailableGateways();
        $suggestedGateway = $this->gatewayFactory->suggestGatewayForCurrency($userCurrency);

        return response()->json([
            'success' => true,
            'user_currency' => $userCurrency,
            'suggested_gateway' => $suggestedGateway,
            'gateways' => $gateways,
        ]);
    }

    /**
     * Initiate an online payment for wallet deposit.
     */
    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.50', 'max:50000000'],
            'currency' => ['nullable', 'string', 'size:3'],
            'gateway' => ['nullable', 'string', 'in:paystack,mock'],
            'callback_url' => ['nullable', 'string', 'max:2048'],
            'metadata' => ['nullable', 'array'],
        ]);

        $callbackUrl = $validated['callback_url'] ?? null;
        if (!empty($callbackUrl)) {
            if (filter_var($callbackUrl, FILTER_VALIDATE_URL)) {
                // Valid absolute URL
            } elseif (str_starts_with($callbackUrl, '/')) {
                // Valid relative path converted to absolute
                $callbackUrl = url($callbackUrl);
            } else {
                // Invalid or unsupported format - let PaymentService use default fallback
                $callbackUrl = null;
            }
        }

        $result = $this->paymentService->initiatePayment(
            user: $request->user(),
            amount: (float) $validated['amount'],
            currency: $validated['currency'] ?? null,
            gateway: $validated['gateway'] ?? null,
            callbackUrl: $callbackUrl,
            metadata: $validated['metadata'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment initialized. Please redirect to authorization URL to complete.',
            'data' => $result,
        ]);
    }

    /**
     * Verify payment status and credit user wallet.
     */
    public function verify(Request $request, string $reference): JsonResponse
    {
        $result = $this->paymentService->verifyPayment($reference);

        if (!$result['success']) {
            $httpCode = ($result['status'] ?? '') === 'pending' ? 200 : 400;
            return response()->json([
                'success' => false,
                'verified' => false,
                'status' => $result['status'] ?? 'failed',
                'reason' => $result['message'] ?? 'Unknown',
                'message' => $result['message'],
            ], $httpCode);
        }

        $wallet = $this->walletService->getOrCreateUserWallet($request->user());

        return response()->json([
            'success' => true,
            'verified' => true,
            'status' => $result['status'],
            'amount' => $result['amount'] ?? (float) ($result['payment']->amount ?? 0),
            'currency' => $result['currency'] ?? $wallet->currency?->code,
            'message' => $result['message'],
            'payment' => $result['payment'],
            'wallet' => new WalletResource($wallet),
        ]);
    }

    /**
     * Bank Transfer endpoint — deprecated with Stripe integration.
     */
    public function chargeBankTransfer(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Bank transfer is not available. Please use Stripe online checkout.',
        ], 400);
    }

    /**
     * USSD endpoint — deprecated with Stripe integration.
     */
    public function chargeUssd(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'USSD payment is not available. Please use Stripe online checkout.',
        ], 400);
    }

    /**
     * Charge Card / initialize card checkout via Stripe.
     */
    public function chargeCard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.50', 'max:50000000'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        try {
            $result = $this->paymentService->initiateCardCharge(
                user: $request->user(),
                amount: (float) $validated['amount'],
                currency: $validated['currency'] ?? 'USD'
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process card payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * OTP challenge endpoint — not required for Stripe.
     */
    public function submitCardOtp(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'OTP submission is not required for Stripe checkout.',
        ], 400);
    }

    /**
     * Simulate payment confirmation for test environments (USSD simulation button).
     */
    public function simulateSuccess(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => ['required', 'string'],
        ]);

        $result = $this->paymentService->simulatePaymentSuccess($validated['reference']);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Mark a pending bank transfer payment as expired.
     */
    public function expirePayment(Request $request, string $reference): JsonResponse
    {
        $result = $this->paymentService->expirePayment($reference);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}


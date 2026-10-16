<?php

namespace App\Application\Services;

use App\Domain\DTOs\PaymentIntentDTO;
use App\Domain\DTOs\PaymentRedirectDTO;
use App\Domain\DTOs\PaymentResultDTO;
use App\Infrastructure\Payments\PaymentGatewayFactory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected PaymentGatewayFactory $gatewayFactory,
        protected WalletService $walletService
    ) {}

    /**
     * Initiate an online payment for account funding or number order.
     */
    public function initiatePayment(
        User $user,
        float $amount,
        ?string $currency = null,
        ?string $gateway = null,
        ?string $callbackUrl = null,
        array $metadata = []
    ): array {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Payment amount must be greater than zero.");
        }

        // Determine currency (user's wallet currency or system default)
        if (empty($currency)) {
            $wallet = $this->walletService->getOrCreateUserWallet($user);
            $currency = $wallet->currency?->code ?? $user->resolveSignupCurrencyCode();
        }
        $currency = strtoupper($currency);

        // Resolve gateway
        if (empty($gateway)) {
            $gateway = $this->gatewayFactory->suggestGatewayForCurrency($currency);
        }
        $adapter = $this->gatewayFactory->make($gateway);

        // Generate unique reference and idempotency key
        $reference = 'PAY_' . Str::ulid();

        // Create pending payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency_code' => $currency,
            'provider' => $adapter->getGatewayCode(),
            'provider_reference' => $reference,
            'status' => 'pending',
            'idempotency_key' => $reference,
        ]);

        $intent = new PaymentIntentDTO(
            amount: $amount,
            currency: $currency,
            userEmail: $user->email,
            reference: $reference,
            userName: $user->name,
            callbackUrl: $callbackUrl,
            metadata: array_merge($metadata, [
                'user_id' => $user->id,
                'payment_id' => $payment->id,
            ])
        );

        $redirect = $adapter->initializePayment($intent);

        $publicKey = config('services.paystack.public_key');

        // Get supported payment channels for Paystack
        $channels = $this->gatewayFactory->getSupportedChannels(
            $adapter->getGatewayCode(),
            $currency
        );

        return [
            'payment_id' => $payment->id,
            'reference' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'charge_amount' => $redirect->gatewayResponse['charge_amount'] ?? $amount,
            'charge_currency' => $redirect->gatewayResponse['charge_currency'] ?? $currency,
            'gateway' => $adapter->getGatewayCode(),
            'authorization_url' => $redirect->authorizationUrl,
            'access_code' => $redirect->accessCode,
            'public_key' => $publicKey,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'channels' => $channels,
            'status' => 'pending',
        ];
    }

    /**
     * Initiate a card charge using Stripe (default gateway).
     * Returns authorization details for frontend Stripe checkout.
     */
    public function initiateCardCharge(
        User $user,
        float $amount,
        ?string $currency = 'USD'
    ): array {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Payment amount must be greater than zero.");
        }

        // Use the default gateway (Stripe)
        return $this->initiatePayment(
            user: $user,
            amount: $amount,
            currency: $currency,
            metadata: ['channel' => 'card']
        );
    }

    /**
     * Simulate payment confirmation for test environments (e.g. USSD simulation button).
     */
    public function simulatePaymentSuccess(string $reference): array
    {
        $payment = Payment::where('provider_reference', $reference)
            ->orWhere('idempotency_key', $reference)
            ->first();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        if ($payment->status === 'paid') {
            $wallet = $this->walletService->getOrCreateUserWallet($payment->user);
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Payment already completed.',
                'reference' => $reference,
                'wallet_balance' => (float) $wallet->available_balance,
            ];
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);
        $user = $payment->user;
        if ($user) {
            try {
                $this->walletService->credit(
                    $user->id,
                    (float) $payment->amount,
                    $payment->idempotency_key,
                    "Payment top-up (simulated confirmation)",
                    'payment',
                    $payment->id
                );
            } catch (\Throwable $e) {
                Log::error("Wallet credit on simulate payment success failed: " . $e->getMessage());
            }

            $wallet = $this->walletService->getOrCreateUserWallet($user);
            return [
                'success' => true,
                'status' => 'success',
                'message' => 'Payment successfully confirmed!',
                'reference' => $reference,
                'wallet_balance' => (float) $wallet->available_balance,
            ];
        }

        return ['success' => true, 'status' => 'success', 'reference' => $reference];
    }

    /**
     * Mark a pending bank transfer payment as expired (e.g. virtual account timeout).
     */
    public function expirePayment(string $reference): array
    {
        $payment = Payment::where('provider_reference', $reference)
            ->orWhere('idempotency_key', $reference)
            ->first();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        if ($payment->status === 'paid') {
            return ['success' => true, 'status' => 'already_paid', 'message' => 'Payment was already completed.'];
        }

        $payment->update(['status' => 'expired']);

        return ['success' => true, 'status' => 'expired', 'message' => 'Payment marked as expired.'];
    }

    /**
     * Verify a payment reference, mark payment as paid, and credit user wallet atomically.
     */
    public function verifyPayment(string $reference): array
    {
        $payment = Payment::where('provider_reference', $reference)
            ->orWhere('idempotency_key', $reference)
            ->first();

        if (!$payment) {
            // Check Deposit model fallback (for deposits initialized via WalletDepositController)
            $deposit = \App\Models\Deposit::where('reference', $reference)->first();
            if ($deposit) {
                $controller = app(\App\Http\Controllers\WalletDepositController::class);
                $res = $controller->processVerification($reference);
                return [
                    'success' => $res['verified'] ?? false,
                    'status' => ($res['verified'] ?? false) ? 'paid' : 'failed',
                    'amount' => $res['amount'] ?? (float) $deposit->amount,
                    'currency' => $deposit->currency,
                    'wallet_balance' => $res['wallet_balance'] ?? null,
                    'message' => $res['message'] ?? 'Payment processed',
                ];
            }

            return [
                'success' => false,
                'status' => 'not_found',
                'message' => "No payment record found with reference [{$reference}].",
            ];
        }

        // Idempotency: If already paid, return successful status immediately without double-crediting
        if ($payment->status === 'paid') {
            return [
                'success' => true,
                'status' => 'already_paid',
                'amount' => (float) $payment->amount,
                'currency' => $payment->currency_code,
                'payment' => $payment,
                'message' => 'Payment has already been processed and credited.',
            ];
        }

        $adapter = $this->gatewayFactory->make($payment->provider);
        $result = $adapter->verifyPayment($reference);

        if (!$result->success) {
            // Check if bank transfer payment has expired without payment arriving
            if ($payment->isExpired()) {
                if ($payment->status !== 'expired') {
                    $payment->update(['status' => 'expired']);
                }
                return [
                    'success' => false,
                    'status' => 'expired',
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency_code,
                    'payment' => $payment,
                    'message' => 'This payment session has expired. Please generate a new deposit.',
                ];
            }

            Log::info("Payment verification pending for [{$reference}]: {$result->message}");
            return [
                'success' => false,
                'status' => 'pending',
                'amount' => (float) $payment->amount,
                'currency' => $payment->currency_code,
                'payment' => $payment,
                'message' => $result->message ?? 'Payment verification was unsuccessful.',
            ];
        }

        // Atomically transition payment to 'paid' and credit wallet
        return DB::transaction(function () use ($payment, $result, $reference) {
            // Lock payment row to prevent race conditions
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if ($lockedPayment->status === 'paid') {
                return [
                    'success' => true,
                    'status' => 'already_paid',
                    'amount' => (float) $lockedPayment->amount,
                    'currency' => $lockedPayment->currency_code,
                    'payment' => $lockedPayment,
                    'message' => 'Payment already credited.',
                ];
            }

            $lockedPayment->update([
                'status' => 'paid',
                'paid_at' => $result->paidAt ? \Carbon\Carbon::parse($result->paidAt) : now(),
                'provider_reference' => $result->gatewayReference ?: $lockedPayment->provider_reference,
            ]);

            // Credit wallet atomically
            $creditRef = ($lockedPayment->provider === 'paystack')
                ? (str_starts_with($lockedPayment->idempotency_key, 'paystack:') ? $lockedPayment->idempotency_key : "paystack:{$lockedPayment->idempotency_key}")
                : $lockedPayment->idempotency_key;

            $tx = $this->walletService->credit(
                $lockedPayment->user_id,
                (float) $lockedPayment->amount,
                $creditRef,
                "Online payment top-up via " . ucfirst($lockedPayment->provider),
                'payment',
                $lockedPayment->id
            );

            // Record payment method in payment_methods table if provided by gateway
            $this->recordPaymentMethod($lockedPayment->user_id, $lockedPayment->provider, $result->rawResponse);

            return [
                'success' => true,
                'status' => 'paid',
                'amount' => (float) $lockedPayment->amount,
                'currency' => $lockedPayment->currency_code,
                'payment' => $lockedPayment->fresh(),
                'transaction' => $tx,
                'message' => 'Payment verified successfully and funds credited to wallet.',
            ];
        });
    }

    /**
     * Store or update customer payment method from gateway authorization details.
     */
    protected function recordPaymentMethod(int $userId, string $provider, array $rawResponse): ?PaymentMethod
    {
        try {
            $type = 'card';
            $providerMethodId = null;
            $providerCustomerId = null;
            $lastFour = null;
            $expMonth = null;
            $expYear = null;

            if ($provider === 'paystack') {
                $auth = $rawResponse['authorization'] ?? [];
                $type = $auth['channel'] ?? 'card';
                $providerMethodId = $auth['authorization_code'] ?? ($rawResponse['reference'] ?? null);
                $providerCustomerId = (string) ($rawResponse['customer']['id'] ?? ($rawResponse['customer']['customer_code'] ?? ''));
                $lastFour = $auth['last4'] ?? null;
                $expMonth = !empty($auth['exp_month']) ? (int) $auth['exp_month'] : null;
                $expYear = !empty($auth['exp_year']) ? (int) $auth['exp_year'] : null;
            } else {
                $type = $provider;
                $providerMethodId = $rawResponse['reference'] ?? ('pm_' . $provider);
                $lastFour = '4242';
                $expMonth = 12;
                $expYear = (int) date('Y') + 3;
            }

            if (empty($providerMethodId)) {
                $providerMethodId = $rawResponse['reference'] ?? ('pm_' . Str::random(16));
            }

            $existingCount = PaymentMethod::where('user_id', $userId)->count();
            $isDefault = ($existingCount === 0);

            return PaymentMethod::updateOrCreate(
                [
                    'user_id' => $userId,
                    'provider' => $provider,
                    'provider_method_id' => $providerMethodId,
                ],
                [
                    'type' => $type,
                    'provider_customer_id' => $providerCustomerId,
                    'last_four' => $lastFour,
                    'expiry_month' => $expMonth,
                    'expiry_year' => $expYear,
                    'is_default' => $isDefault,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning("Failed to record payment method: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Process asynchronous payment webhook from providers.
     */
    public function processWebhook(string $gateway, array $payload, array $headers = []): array
    {
        $adapter = $this->gatewayFactory->make($gateway);
        $result = $adapter->handleWebhook($payload, $headers);

        if (!$result->success || empty($result->reference)) {
            return [
                'success' => false,
                'message' => $result->message ?? 'Webhook discarded or invalid.',
            ];
        }

        $payment = Payment::where('provider_reference', $result->reference)
            ->orWhere('idempotency_key', $result->reference)
            ->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => "Payment not found for reference [{$result->reference}].",
            ];
        }

        if ($payment->status === 'paid') {
            return [
                'success' => true,
                'status' => 'already_paid',
                'amount' => (float) $payment->amount,
                'currency' => $payment->currency_code,
                'payment' => $payment,
                'message' => 'Payment has already been processed and credited.',
            ];
        }

        // Atomically fulfill payment and credit user wallet
        return DB::transaction(function () use ($payment, $result) {
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if ($lockedPayment->status === 'paid') {
                return [
                    'success' => true,
                    'status' => 'already_paid',
                    'amount' => (float) $lockedPayment->amount,
                    'currency' => $lockedPayment->currency_code,
                    'payment' => $lockedPayment,
                    'message' => 'Payment already credited.',
                ];
            }

            $lockedPayment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'provider_reference' => $result->gatewayReference ?: $lockedPayment->provider_reference,
            ]);

            $tx = $this->walletService->credit(
                $lockedPayment->user_id,
                (float) $lockedPayment->amount,
                $lockedPayment->idempotency_key,
                "Online payment top-up via " . ucfirst($lockedPayment->provider),
                'payment',
                $lockedPayment->id
            );

            // Record payment method if available in webhook data
            if (!empty($result->data)) {
                $this->recordPaymentMethod($lockedPayment->user_id, $lockedPayment->provider, $result->data);
            }

            return [
                'success' => true,
                'status' => 'paid',
                'amount' => (float) $lockedPayment->amount,
                'currency' => $lockedPayment->currency_code,
                'payment' => $lockedPayment->fresh(),
                'transaction' => $tx,
                'message' => 'Payment fulfilled via webhook successfully.',
            ];
        });
    }
}


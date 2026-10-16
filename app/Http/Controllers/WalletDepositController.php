<?php

namespace App\Http\Controllers;

use App\Application\Services\WalletService;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Native\Mobile\Facades\Browser;

class WalletDepositController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Open authorization URL using NativePHP Browser::inApp()
     * POST /wallet/deposit/open-in-app
     */
    public function openInApp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
        ]);

        $url = $validated['url'];
        $opened = false;

        if (class_exists(\Native\Mobile\Facades\Browser::class)) {
            try {
                \Native\Mobile\Facades\Browser::inApp($url);
                $opened = true;
                Log::info('[WalletDeposit] Browser::inApp called for In-App checkout', ['url' => $url, 'opened' => true]);
            } catch (\Throwable $e) {
                Log::warning('[WalletDeposit] Browser::inApp failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => true,
            'opened' => $opened,
            'url' => $url,
        ]);
    }

    /**
     * 1. Initialize Payment Transaction (Paystack Only).
     * POST /transaction/initialize
     */
    public function initialize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'email' => ['nullable', 'email'],
            'currency' => ['nullable', 'string', 'size:3'],
            'gateway' => ['nullable', 'string'],
            'open_in_app' => ['nullable', 'boolean'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $email = $validated['email'] ?? $user?->email;
        $amount = (float) $validated['amount'];
        $currency = strtoupper($validated['currency'] ?? 'USD');
        $openInApp = (bool) ($validated['open_in_app'] ?? true);

        if (!$email) {
            return response()->json([
                'status' => false,
                'message' => 'User email is required to initialize payment.',
            ], 422);
        }

        // Initialize Paystack payment (primary gateway)
        return $this->initializePaystack($user, $email, $amount, $currency, $openInApp);
    }

    /**
     * Initialize Flutterwave payment transaction.
     */
    protected function initializeFlutterwave(User $user, string $email, float $amount, string $currency, bool $openInApp = true): JsonResponse
    {
        $flutterwaveSecret = (string) config('services.flutterwave.secret_key');
        $flutterwavePublic = (string) config('services.flutterwave.public_key');

        if (empty($flutterwaveSecret)) {
            return response()->json([
                'status' => false,
                'message' => 'Flutterwave is not configured on the server.',
            ], 422);
        }

        $reference = 'DEP_FLW_' . Str::ulid();
        $callbackUrl = route('wallet.deposit.callback');

        try {
            // Create Flutterwave payment link
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $flutterwaveSecret,
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => $callbackUrl . '?reference=' . $reference,
                'customer' => [
                    'email' => $email,
                    'name' => $user->name ?? 'Customer',
                ],
                'customizations' => [
                    'title' => 'Wallet Deposit',
                    'description' => 'Add funds to your wallet',
                ],
                'meta' => [
                    'reference' => $reference,
                    'user_id' => (string) ($user->id ?? ''),
                ],
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                $responseData = $response->json('data', []);
                $checkoutUrl = $responseData['link'] ?? '';

                Deposit::create([
                    'user_id' => $user->id,
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'pending',
                    'gateway_response' => [
                        'gateway' => 'flutterwave',
                        'url' => $checkoutUrl,
                    ],
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Flutterwave checkout initialized successfully.',
                    'data' => [
                        'gateway' => 'flutterwave',
                        'authorization_url' => $checkoutUrl,
                        'reference' => $reference,
                        'public_key' => $flutterwavePublic,
                        'amount' => $amount,
                    ],
                ]);
            }

            $errMsg = $response->json('message') ?? 'Could not initialize Flutterwave payment.';
            return response()->json([
                'status' => false,
                'message' => $errMsg,
            ], 400);

        } catch (\Throwable $e) {
            Log::error('[WalletDeposit] Flutterwave initialize exception: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to initialize payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initialize Stripe payment transaction (DEPRECATED - keeping for backwards compatibility).
     */
    protected function initializeStripe(User $user, string $email, float $amount, string $currency, bool $openInApp = true): JsonResponse
    {
        $stripeSecret = (string) config('services.stripe.secret_key');
        $stripePublic = (string) config('services.stripe.public_key');

        if (empty($stripeSecret)) {
            return response()->json([
                'status' => false,
                'message' => 'Stripe is not configured on the server.',
            ], 422);
        }

        $reference = 'DEP_STRIPE_' . Str::ulid();

        // Map currency to Stripe's smallest unit multiplier
        $zeroDecimalCurrencies = ['JPY', 'KRW', 'BIF', 'CLP', 'GNF', 'ISK', 'KMF', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        $multiplier = in_array(strtoupper($currency), $zeroDecimalCurrencies) ? 1 : 100;
        $stripeAmount = (int) round($amount * $multiplier);
        $callbackUrl = route('wallet.deposit.callback');

        try {
            // Create Stripe Checkout Session
            $sessionRes = Http::withHeaders([
                'Authorization' => 'Bearer ' . $stripeSecret,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://api.stripe.com/v1/checkout/sessions', [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency == 'NGN' ? 'usd' : $currency),
                        'product_data' => [
                            'name' => 'Wallet Deposit',
                        ],
                        'unit_amount' => $stripeAmount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $callbackUrl . '?reference=' . $reference,
                'cancel_url' => $callbackUrl . '?canceled=true&reference=' . $reference,
                'customer_email' => $email,
                'client_reference_id' => $reference,
                'metadata' => [
                    'reference' => $reference,
                    'user_id' => (string) ($user->id ?? ''),
                ],
            ]);

            if ($sessionRes->successful()) {
                $sessionData = $sessionRes->json();
                $checkoutUrl = $sessionData['url'] ?? '';
                $sessionId = $sessionData['id'] ?? '';

                Deposit::create([
                    'user_id' => $user->id,
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'pending',
                    'gateway_response' => [
                        'gateway' => 'stripe',
                        'session_id' => $sessionId,
                        'url' => $checkoutUrl,
                    ],
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Stripe checkout initialized successfully.',
                    'data' => [
                        'gateway' => 'stripe',
                        'authorization_url' => $checkoutUrl,
                        'session_id' => $sessionId,
                        'reference' => $reference,
                        'public_key' => $stripePublic,
                        'amount' => $amount,
                    ],
                ]);
            }

            // Fallback: PaymentIntent
            $piResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $stripeSecret,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $stripeAmount,
                'currency' => strtolower($currency == 'NGN' ? 'usd' : $currency),
                'description' => "Deposit to {$user->name} wallet ({$reference})",
                'metadata[user_id]' => (string) ($user->id ?? ''),
                'metadata[reference]' => $reference,
                'metadata[email]' => $email,
                'payment_method_types[]' => 'card',
            ]);

            if ($piResponse->successful()) {
                $piData = $piResponse->json();
                $clientSecret = $piData['client_secret'] ?? '';
                $paymentIntentId = $piData['id'] ?? '';

                Deposit::create([
                    'user_id' => $user->id,
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'pending',
                    'gateway_response' => [
                        'gateway' => 'stripe',
                        'payment_intent_id' => $paymentIntentId,
                        'client_secret' => $clientSecret,
                    ],
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Stripe checkout initialized successfully.',
                    'data' => [
                        'gateway' => 'stripe',
                        'client_secret' => $clientSecret,
                        'payment_intent_id' => $paymentIntentId,
                        'reference' => $reference,
                        'public_key' => $stripePublic,
                        'amount' => $amount,
                    ],
                ]);
            }

            $errMsg = $sessionRes->json('error.message') ?? $piResponse->json('error.message') ?? 'Could not initialize Stripe payment.';
            return response()->json([
                'status' => false,
                'message' => $errMsg,
            ], 400);

        } catch (\Throwable $e) {
            Log::error('[WalletDeposit] Stripe initialize exception: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to initialize payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initialize Paystack transaction for in-app popup checkout.
     */
    protected function initializePaystack(User $user, string $email, float $amount, string $currency, bool $openInApp = true): JsonResponse
    {
        $secretKey = (string) config('services.paystack.secret_key');
        if (empty($secretKey)) {
            return response()->json([
                'status' => false,
                'message' => 'Paystack secret key is not configured on the server.',
            ], 500);
        }

        $reference = 'DEP_' . Str::ulid();
        $callbackUrl = route('wallet.deposit.callback');

        $cancelActionUrl = route('wallet.deposit.callback', ['canceled' => 1, 'reference' => $reference]);

        $baseUrl = rtrim((string) config('services.paystack.payment_url', 'https://api.paystack.co'), '/');

        try {
            $response = Http::withToken($secretKey)
                ->timeout(15)
                ->post("{$baseUrl}/transaction/initialize", [
                    'email' => $email,
                    'amount' => (int) round($amount * 100),
                    'currency' => $currency,
                    'reference' => $reference,
                    'callback_url' => $callbackUrl,
                    'cancel_action' => $cancelActionUrl,
                    'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
                    'metadata' => [
                        'user_id' => (string) ($user?->id ?? ''),
                        'reference' => $reference,
                        'customer_name' => $user?->name ?? 'User',
                        'cancel_action' => $cancelActionUrl,
                    ],
                ]);

            if (!$response->successful() || !$response->json('status')) {
                $errorMsg = $response->json('message') ?? 'Could not initialize Paystack transaction.';
                Log::error('[WalletDeposit] Paystack transaction initialize failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return response()->json([
                    'status' => false,
                    'message' => $errorMsg,
                ], 400);
            }

            $responseData = $response->json('data', []);
            $authorizationUrl = $responseData['authorization_url'] ?? '';
            $accessCode = $responseData['access_code'] ?? '';

            // Record pending deposit in deposits table
            Deposit::create([
                'user_id' => $user->id,
                'reference' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'gateway_response' => [
                    'gateway' => 'paystack',
                    'access_code' => $accessCode,
                    'url' => $authorizationUrl,
                ],
            ]);

            // Open authorization URL directly in-app without permission prompt (SFSafariViewController / Custom Tabs)
            $openedInApp = false;
            if ($openInApp && class_exists(\Native\Mobile\Facades\Browser::class)) {
                try {
                    \Native\Mobile\Facades\Browser::inApp($authorizationUrl);
                    $openedInApp = true;
                    Log::info('[WalletDeposit] Paystack transaction initialized with Browser::inApp', [
                        'reference' => $reference,
                        'url' => $authorizationUrl,
                        'opened' => true,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('[WalletDeposit] Browser::inApp failed in initializePaystack: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Paystack checkout initialized successfully.',
                'data' => [
                    'gateway' => 'paystack',
                    'authorization_url' => $authorizationUrl,
                    'access_code' => $accessCode,
                    'reference' => $reference,
                    'callback_url' => $callbackUrl,
                    'public_key' => (string) config('services.paystack.public_key', ''),
                    'opened_in_app' => $openedInApp,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('[WalletDeposit] Paystack exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to initialize Paystack: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * GET /wallet/balance
     * Returns current authenticated user's wallet balance as JSON with Cache-Control: no-store.
     */
    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401)->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        $balance = $this->walletService->getBalance($user->id);
        $wallet = $this->walletService->getOrCreateUserWallet($user);

        return response()->json([
            'status' => true,
            'balance' => (float) $balance,
            'available_balance' => (float) $balance,
            'currency' => $wallet->currency?->code ?? 'NGN',
            'symbol' => $wallet->currency?->symbol ?? '₦',
            'formatted_balance' => number_format((float) $balance, 2),
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * GET /wallet/deposit-status/{reference}
     * Checks deposit status by reference (no auth session required), verifying with provider if pending.
     */
    public function depositStatus(string $reference): JsonResponse
    {
        $deposit = Deposit::where('reference', $reference)->first();

        if (!$deposit) {
            return response()->json([
                'status' => false,
                'deposit_status' => 'not_found',
                'verified' => false,
                'message' => "Deposit reference [{$reference}] not found.",
            ], 404)->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        if ($deposit->isSuccess()) {
            $balance = $this->walletService->getBalance($deposit->user_id);
            return response()->json([
                'status' => true,
                'deposit_status' => 'success',
                'verified' => true,
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
                'wallet_balance' => (float) $balance,
                'message' => 'Deposit confirmed successfully.',
            ])->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        if ($deposit->status === 'cancelled') {
            return response()->json([
                'status' => false,
                'deposit_status' => 'cancelled',
                'verified' => false,
                'canceled' => true,
                'message' => 'Deposit was cancelled.',
            ])->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        // Deposit is pending: attempt server-side verification using canonical method
        $result = $this->creditDepositIfVerified($reference);

        if ($result['verified'] ?? false) {
            return response()->json([
                'status' => true,
                'deposit_status' => 'success',
                'verified' => true,
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
                'wallet_balance' => (float) ($result['wallet_balance'] ?? $this->walletService->getBalance($deposit->user_id)),
                'message' => 'Deposit confirmed successfully.',
            ])->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        return response()->json([
            'status' => true,
            'deposit_status' => 'pending',
            'verified' => false,
            'pending' => true,
            'message' => $result['message'] ?? 'Waiting for payment confirmation.',
        ])->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * 2. Server-side Verification with Idempotency.
     * GET or POST /wallet/verify/{reference}
     */
    public function verify(Request $request, string $reference): JsonResponse
    {
        $result = $this->creditDepositIfVerified($reference);
        $code = $result['code'] ?? 200;
        unset($result['code']);
        return response()->json($result, $code)->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Process verification alias for compatibility.
     */
    public function processVerification(string $reference): array
    {
        return $this->creditDepositIfVerified($reference);
    }

    /**
     * Core verification & crediting logic (Server Source of Truth).
     * Single method used by both the Paystack webhook (charge.success) and /wallet/verify / deposit-status.
     * Idempotent: DB transaction, row-level locking (lockForUpdate), validates amount & currency before crediting.
     */
    public function creditDepositIfVerified(string $reference, ?array $paystackData = null): array
    {
        $deposit = Deposit::where('reference', $reference)->first();

        if (!$deposit) {
            // Fallback: check Payment model (for payments initialized via PaymentService)
            $payment = \App\Models\Payment::where('provider_reference', $reference)
                ->orWhere('idempotency_key', $reference)
                ->first();

            if ($payment) {
                $paymentService = app(\App\Application\Services\PaymentService::class);
                $pRes = $paymentService->verifyPayment($reference);
                return [
                    'status' => $pRes['success'] ?? false,
                    'verified' => $pRes['success'] ?? false,
                    'code' => 200,
                    'message' => $pRes['message'] ?? 'Payment verified',
                    'amount' => (float) ($pRes['amount'] ?? $payment->amount),
                    'currency' => $payment->currency_code,
                    'wallet_balance' => (float) ($pRes['wallet_balance'] ?? 0),
                ];
            }

            return [
                'status' => false,
                'verified' => false,
                'code' => 200,
                'pending' => true,
                'message' => "Deposit reference [{$reference}] pending or awaiting creation.",
            ];
        }

        // Idempotency / Fast Path: If already credited, return success immediately
        if ($deposit->isSuccess()) {
            $walletBalance = $this->walletService->getBalance($deposit->user_id);
            return [
                'status' => true,
                'verified' => true,
                'already_processed' => true,
                'code' => 200,
                'message' => 'Deposit has already been verified and credited.',
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
                'wallet_balance' => (float) $walletBalance,
            ];
        }

        // Fast Path: If marked cancelled, return cancelled status immediately
        if ($deposit->status === 'cancelled') {
            return [
                'status' => false,
                'verified' => false,
                'canceled' => true,
                'code' => 200,
                'message' => 'Deposit transaction was cancelled.',
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
            ];
        }

        // Handle other gateways (Flutterwave, Stripe legacy)
        $depositGateway = strtolower($deposit->gateway_response['gateway'] ?? '');
        if (str_starts_with($reference, 'DEP_FLW_') || $depositGateway === 'flutterwave') {
            return $this->verifyFlutterwaveDeposit($deposit);
        }
        if (str_starts_with($reference, 'DEP_STRIPE_') || str_starts_with($reference, 'DEP_VERIFY_REF_') || $depositGateway === 'stripe') {
            return $this->verifyStripeDeposit($deposit);
        }

        // Paystack Verification Logic
        $data = $paystackData;

        // If webhook did not supply verified payload, query Paystack verify API
        if (!$data) {
            $secretKey = (string) config('services.paystack.secret_key');
            if (empty($secretKey)) {
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 500,
                    'message' => 'Paystack secret key is not configured.',
                ];
            }

            $baseUrl = rtrim((string) config('services.paystack.payment_url', 'https://api.paystack.co'), '/');
            $response = Http::withToken($secretKey)
                ->timeout(15)
                ->get("{$baseUrl}/transaction/verify/" . rawurlencode($reference));

            if (!$response->successful() || !$response->json('status')) {
                $msg = $response->json('message') ?? 'Waiting for payment confirmation.';
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 200,
                    'pending' => true,
                    'message' => $msg,
                ];
            }

            $data = $response->json('data', []);
        }

        $paystackStatus = strtolower($data['status'] ?? '');
        if ($paystackStatus !== 'success') {
            return [
                'status' => false,
                'verified' => false,
                'code' => 200,
                'pending' => true,
                'message' => "Payment is {$paystackStatus}.",
            ];
        }

        // Verify Amount and Currency against the pending transaction before crediting
        $paystackAmountKobo = (int) ($data['amount'] ?? 0);
        $expectedAmountKobo = (int) round(((float) $deposit->amount) * 100);
        $paystackCurrency = strtoupper((string) ($data['currency'] ?? ''));
        $expectedCurrency = strtoupper((string) $deposit->currency);

        if ($paystackAmountKobo !== $expectedAmountKobo) {
            Log::warning("[WalletDeposit] Amount mismatch for reference {$reference}: expected {$expectedAmountKobo} kobo, got {$paystackAmountKobo} kobo.");
            return [
                'status' => false,
                'verified' => false,
                'code' => 422,
                'message' => 'Payment amount does not match deposit transaction.',
            ];
        }

        if (!empty($paystackCurrency) && !empty($expectedCurrency) && $paystackCurrency !== $expectedCurrency) {
            Log::warning("[WalletDeposit] Currency mismatch for reference {$reference}: expected {$expectedCurrency}, got {$paystackCurrency}.");
            return [
                'status' => false,
                'verified' => false,
                'code' => 422,
                'message' => 'Payment currency does not match deposit transaction.',
            ];
        }

        $channel = $data['channel'] ?? 'paystack';

        // Atomically lock pending deposit row and credit wallet inside a database transaction
        return DB::transaction(function () use ($deposit, $data, $channel) {
            /** @var Deposit $lockedDeposit */
            $lockedDeposit = Deposit::where('id', $deposit->id)->lockForUpdate()->first();

            if ($lockedDeposit->isSuccess()) {
                $walletBalance = $this->walletService->getBalance($lockedDeposit->user_id);
                return [
                    'status' => true,
                    'verified' => true,
                    'already_processed' => true,
                    'code' => 200,
                    'message' => 'Deposit has already been verified and credited.',
                    'amount' => (float) $lockedDeposit->amount,
                    'currency' => $lockedDeposit->currency,
                    'wallet_balance' => (float) $walletBalance,
                ];
            }

            if ($lockedDeposit->status === 'cancelled') {
                return [
                    'status' => false,
                    'verified' => false,
                    'canceled' => true,
                    'code' => 200,
                    'message' => 'Deposit transaction was cancelled.',
                    'amount' => (float) $lockedDeposit->amount,
                    'currency' => $lockedDeposit->currency,
                ];
            }

            $paidAt = !empty($data['paid_at']) ? \Carbon\Carbon::parse($data['paid_at']) : now();

            $lockedDeposit->update([
                'status' => 'success',
                'channel' => $channel,
                'paid_at' => $paidAt,
                'gateway_response' => $data,
            ]);

            // Credit wallet using unique reference idempotency with paystack:{reference} prefix
            $paystackRef = str_starts_with($lockedDeposit->reference, 'paystack:')
                ? $lockedDeposit->reference
                : "paystack:{$lockedDeposit->reference}";

            $this->walletService->credit(
                $lockedDeposit->user_id,
                (float) $lockedDeposit->amount,
                $paystackRef,
                'Wallet deposit via Paystack Online',
                'deposit',
                $lockedDeposit->id
            );

            $walletBalance = $this->walletService->getBalance($lockedDeposit->user_id);

            return [
                'status' => true,
                'verified' => true,
                'code' => 200,
                'message' => 'Payment verified successfully and wallet credited.',
                'amount' => (float) $lockedDeposit->amount,
                'currency' => $lockedDeposit->currency,
                'wallet_balance' => (float) $walletBalance,
            ];
        });
    }

    /**
     * Verify Flutterwave transaction and credit wallet.
     */
    protected function verifyFlutterwaveDeposit(Deposit $deposit): array
    {
        $flutterwaveSecret = (string) config('services.flutterwave.secret_key');
        if (empty($flutterwaveSecret)) {
            return [
                'status' => false,
                'verified' => false,
                'code' => 500,
                'message' => 'Flutterwave secret key is not configured.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $flutterwaveSecret,
            ])->timeout(15)->get("https://api.flutterwave.com/v3/transactions/verify_by_reference", [
                'tx_ref' => $deposit->reference,
            ]);

            if (!$response->successful() || $response->json('status') !== 'success') {
                $msg = $response->json('message') ?? 'Waiting for payment confirmation.';
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 200,
                    'pending' => true,
                    'message' => $msg,
                ];
            }

            $data = $response->json('data', []);
            $status = strtolower($data['status'] ?? '');

            if ($status !== 'successful') {
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 200,
                    'pending' => true,
                    'message' => "Payment is {$status}.",
                ];
            }

            $paidAmount = (float) ($data['amount'] ?? 0);
            $currency = strtoupper($data['currency'] ?? $deposit->currency ?? 'NGN');
            $channel = $data['payment_type'] ?? 'flutterwave';

            $wallet = DB::transaction(function () use ($deposit, $data, $paidAmount, $channel) {
                /** @var Deposit $lockedDeposit */
                $lockedDeposit = Deposit::where('id', $deposit->id)->lockForUpdate()->first();

                if ($lockedDeposit->isSuccess()) {
                    return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
                }

                $lockedDeposit->update([
                    'status' => 'success',
                    'channel' => $channel,
                    'paid_at' => now(),
                    'gateway_response' => $data,
                ]);

                $this->walletService->credit(
                    $lockedDeposit->user_id,
                    $paidAmount > 0 ? $paidAmount : (float) $lockedDeposit->amount,
                    $lockedDeposit->reference,
                    'Wallet deposit via Flutterwave',
                    'deposit',
                    $lockedDeposit->id
                );

                return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
            });

            return [
                'status' => true,
                'verified' => true,
                'code' => 200,
                'message' => 'Payment verified successfully and wallet credited.',
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
                'wallet_balance' => (float) $wallet->available_balance,
            ];
        } catch (\Throwable $e) {
            Log::error('Flutterwave verification exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'verified' => false,
                'code' => 500,
                'message' => 'Exception verifying Flutterwave payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Stripe PaymentIntent or Checkout Session and credit wallet (LEGACY).
     */
    protected function verifyStripeDeposit(Deposit $deposit): array
    {
        $stripeSecret = (string) config('services.stripe.secret_key');
        if (empty($stripeSecret)) {
            return [
                'status' => false,
                'verified' => false,
                'code' => 500,
                'message' => 'Stripe secret key is not configured.',
            ];
        }

        $paymentIntentId = $deposit->gateway_response['payment_intent_id'] ?? null;
        if (!$paymentIntentId) {
            // Check Stripe Checkout Sessions as fallback
            $csResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $stripeSecret,
            ])->timeout(15)->get("https://api.stripe.com/v1/checkout/sessions", [
                'client_reference_id' => $deposit->reference,
            ]);

            if ($csResponse->successful()) {
                $csData = $csResponse->json('data.0', []);
                if (!empty($csData) && ($csData['payment_status'] ?? '') === 'paid') {
                    $currency = strtoupper($csData['currency'] ?? $deposit->currency ?? 'NGN');
                    $paidAmount = (float) (($csData['amount_total'] ?? 0) / 100);

                    $wallet = DB::transaction(function () use ($deposit, $csData, $paidAmount) {
                        /** @var Deposit $lockedDeposit */
                        $lockedDeposit = Deposit::where('id', $deposit->id)->lockForUpdate()->first();
                        if ($lockedDeposit->isSuccess()) {
                            return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
                        }
                        $lockedDeposit->update([
                            'status' => 'success',
                            'channel' => 'stripe',
                            'paid_at' => now(),
                            'gateway_response' => $csData,
                        ]);
                        $this->walletService->credit(
                            $lockedDeposit->user_id,
                            $paidAmount > 0 ? $paidAmount : (float) $lockedDeposit->amount,
                            $lockedDeposit->reference,
                            'Wallet deposit via Stripe Checkout',
                            'deposit',
                            $lockedDeposit->id
                        );
                        return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
                    });

                    return [
                        'status' => true,
                        'verified' => true,
                        'code' => 200,
                        'message' => 'Stripe payment verified successfully.',
                        'amount' => (float) $deposit->amount,
                        'currency' => $deposit->currency,
                        'wallet_balance' => (float) $wallet->available_balance,
                    ];
                }
            }

            return [
                'status' => false,
                'verified' => false,
                'code' => 200,
                'pending' => true,
                'message' => 'Stripe payment is awaiting confirmation.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $stripeSecret,
            ])->timeout(15)->get("https://api.stripe.com/v1/payment_intents/{$paymentIntentId}");

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?? 'Could not verify payment with Stripe.';
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 400,
                    'message' => $msg,
                ];
            }

            $data = $response->json();
            $status = strtolower($data['status'] ?? '');

            if ($status !== 'succeeded') {
                return [
                    'status' => false,
                    'verified' => false,
                    'code' => 200,
                    'pending' => true,
                    'message' => "Stripe payment status is {$status}.",
                ];
            }

            $currency = strtoupper($data['currency'] ?? $deposit->currency ?? 'NGN');
            $zeroDecimalCurrencies = ['JPY', 'KRW', 'BIF', 'CLP', 'GNF', 'ISK', 'KMF', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
            $divisor = in_array($currency, $zeroDecimalCurrencies) ? 1 : 100;
            $paidAmount = (float) (($data['amount_received'] ?? $data['amount'] ?? 0) / $divisor);

            $wallet = DB::transaction(function () use ($deposit, $data, $paidAmount) {
                /** @var Deposit $lockedDeposit */
                $lockedDeposit = Deposit::where('id', $deposit->id)->lockForUpdate()->first();

                if ($lockedDeposit->isSuccess()) {
                    return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
                }

                $lockedDeposit->update([
                    'status' => 'success',
                    'channel' => 'stripe_card',
                    'paid_at' => now(),
                    'gateway_response' => array_merge($lockedDeposit->gateway_response ?? [], ['stripe_verified' => $data]),
                ]);

                $this->walletService->credit(
                    $lockedDeposit->user_id,
                    $paidAmount > 0 ? $paidAmount : (float) $lockedDeposit->amount,
                    $lockedDeposit->reference,
                    'Wallet deposit via Stripe Card',
                    'deposit',
                    $lockedDeposit->id
                );

                return $this->walletService->getOrCreateUserWallet($lockedDeposit->user);
            });

            return [
                'status' => true,
                'verified' => true,
                'code' => 200,
                'message' => 'Payment verified successfully and wallet credited.',
                'amount' => (float) $deposit->amount,
                'currency' => $deposit->currency,
                'wallet_balance' => (float) $wallet->available_balance,
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe verification exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'verified' => false,
                'code' => 500,
                'message' => 'Exception verifying Stripe payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 3. Webhook Handler for payment gateways.
     * POST /webhooks/flutterwave
     * POST /webhooks/paystack (legacy)
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['event'] ?? '';
        $data = $payload['data'] ?? [];

        Log::info("Payment webhook received: {$event}");

        // Flutterwave webhook handling
        if ($event === 'charge.completed' || isset($payload['txRef'])) {
            $reference = $data['tx_ref'] ?? $payload['txRef'] ?? null;
            $status = $data['status'] ?? $payload['status'] ?? '';
            
            if ($reference && strtolower($status) === 'successful') {
                $this->processVerification($reference);
            }
            
            return response()->json(['status' => true, 'received' => true], 200);
        }

        // Validate Paystack HMAC signature if provided
        $signature = $request->header('x-paystack-signature');
        $secretKey = (string) config('services.paystack.secret_key');
        if ($signature && $secretKey) {
            $computed = hash_hmac('sha512', $request->getContent(), $secretKey);
            if (!hash_equals($computed, $signature)) {
                Log::warning('Paystack webhook signature verification failed.');
                return response()->json(['status' => false, 'message' => 'Invalid signature'], 400);
            }
        }

        if ($event === 'charge.success') {
            $reference = $data['reference'] ?? null;
            if ($reference) {
                $this->creditDepositIfVerified($reference, $data);
            }
        }

        // Stripe event handling (legacy)
        $stripeType = $payload['type'] ?? '';
        if ($stripeType === 'payment_intent.succeeded') {
            $intentObject = $payload['data']['object'] ?? [];
            $stripeRef = $intentObject['metadata']['reference'] ?? null;
            $intentId = $intentObject['id'] ?? null;

            if ($stripeRef) {
                $this->processVerification($stripeRef);
            } elseif ($intentId) {
                $deposit = Deposit::where('gateway_response->payment_intent_id', $intentId)->first();
                if ($deposit) {
                    $this->processVerification($deposit->reference);
                }
            }
        }

        return response()->json(['status' => true, 'received' => true], 200);
    }

    /**
     * 4. In-App Browser Callback Page.
     * GET /wallet/callback
     * Identifies payment by reference only (does NOT depend on user session).
     * Renders a short confirmation message only — NEVER redirects to full dashboard inside in-app browser.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?: ($request->query('trxref') ?: '');
        $isCanceled = $request->boolean('canceled') 
            || $request->query('canceled') === '1' 
            || $request->query('canceled') === 'true'
            || $request->query('status') === 'cancelled';
        $verificationResult = null;
        $deposit = null;

        if ($isCanceled) {
            if (!empty($reference)) {
                $deposit = Deposit::where('reference', $reference)->first();

                if ($deposit && $deposit->status === 'pending') {
                    $deposit->update([
                        'status' => 'cancelled',
                        'gateway_response' => array_merge($deposit->gateway_response ?? [], [
                            'cancelled_at' => now()->toIso8601String(),
                            'cancelled_by_user' => true,
                        ]),
                    ]);
                }
            }
        } elseif (!empty($reference)) {
            $deposit = Deposit::where('reference', $reference)->first();

            // Run canonical verification directly without touching session
            $verificationResult = $this->creditDepositIfVerified($reference);
            if ($deposit) {
                $deposit->refresh();
            }
        }

        return response()->view('native.payment-callback', compact('reference', 'deposit', 'verificationResult', 'isCanceled'))
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
    }
}

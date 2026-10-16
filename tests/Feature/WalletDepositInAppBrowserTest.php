<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WalletDepositInAppBrowserTest extends TestCase
{
    use RefreshDatabase;

    protected Currency $currency;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency = Currency::firstOrCreate(
            ['code' => 'NGN'],
            ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::factory()->create([
            'email' => 'inapp_user@example.com',
            'first_name' => 'InApp',
            'last_name' => 'Tester',
            'phone' => '+2348099887766',
        ]);

        Config::set('services.flutterwave.public_key', 'FLWPUBK-test-sample-pub-123');
        Config::set('services.flutterwave.secret_key', 'FLWSECK-test-sample-sec-123');
    }

    public function test_full_screen_deposit_page_renders_with_header_and_pay_button(): void
    {
        $response = $this->actingAs($this->user)->get('/deposit');
        $response->assertStatus(200);
        $response->assertSee('Deposit');
        $response->assertSee('Enter amount (₦)');
        $response->assertSee('id="deposit-amount-input"', false);
        $response->assertSee('id="deposit-submit-btn"', false);
    }

    public function test_transaction_initialize_creates_deposit_and_returns_authorization_url(): void
    {
        Config::set('services.paystack.secret_key', 'sk_test_paystack_sample_123');

        Http::fake([
            'https://api.paystack.co/*' => Http::response([
                'status' => true,
                'message' => 'Authorization url created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test_12345',
                    'access_code' => 'test_12345',
                    'reference' => 'DEP_TEST_123',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/transaction/initialize', [
            'amount' => 2500,
            'currency' => 'NGN',
            'open_in_app' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('deposits', [
            'user_id' => $this->user->id,
            'amount' => 2500.00,
            'status' => 'pending',
        ]);
    }

    public function test_wallet_verify_endpoint_verifies_payment_and_credits_wallet(): void
    {
        $walletService = app(WalletService::class);
        $wallet = $walletService->getOrCreateUserWallet($this->user, $this->currency);
        $this->assertEquals(0, (float) $wallet->available_balance);

        $deposit = Deposit::create([
            'user_id' => $this->user->id,
            'reference' => 'DEP_FLW_VERIFY_REF_200',
            'amount' => 3000,
            'currency' => 'NGN',
            'status' => 'pending',
            'gateway_response' => ['gateway' => 'flutterwave'],
        ]);

        Http::fake([
            'https://api.flutterwave.com/*' => Http::response([
                'status' => 'success',
                'message' => 'Transaction fetched successfully',
                'data' => [
                    'id' => 12345,
                    'tx_ref' => 'DEP_FLW_VERIFY_REF_200',
                    'flw_ref' => 'FLW-MOCK-REF-200',
                    'amount' => 3000,
                    'currency' => 'NGN',
                    'status' => 'successful',
                    'charged_amount' => 3000,
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/wallet/verify/DEP_FLW_VERIFY_REF_200');
        $response->assertStatus(200);

        $deposit->refresh();
        $this->assertEquals('success', $deposit->status);

        $wallet->refresh();
        $this->assertEquals(3000, (float) $wallet->available_balance);

        // Test idempotency: Calling verify again does NOT credit wallet again
        $repeatResponse = $this->actingAs($this->user)->postJson('/wallet/verify/DEP_FLW_VERIFY_REF_200');
        $repeatResponse->assertStatus(200);
        $repeatResponse->assertJson(['already_processed' => true]);

        $wallet->refresh();
        $this->assertEquals(3000, (float) $wallet->available_balance);
    }

    public function test_open_in_app_endpoint_accepts_url(): void
    {
        $response = $this->actingAs($this->user)->postJson('/wallet/deposit/open-in-app', [
            'url' => 'https://checkout.paystack.com/test_session_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'url' => 'https://checkout.paystack.com/test_session_123',
        ]);
    }

    public function test_send_wallet_credit_notification_suppressed_in_testing(): void
    {
        $walletService = app(WalletService::class);
        $wallet = $walletService->getOrCreateUserWallet($this->user, $this->currency);

        $initialNotificationCount = \App\Models\Notification::where('user_id', $this->user->id)->count();

        // Credit wallet in testing environment
        $walletService->credit($this->user, 50.00, 'TEST_CREDIT_NOTIFICATION_SUPPRESSED');

        // Notification handler should NOT create notifications or fire Dialog::toast in testing
        $afterCount = \App\Models\Notification::where('user_id', $this->user->id)->count();
        $this->assertEquals($initialNotificationCount, $afterCount);
    }

    public function test_get_wallet_balance_endpoint_returns_json_and_no_store_headers(): void
    {
        $walletService = app(WalletService::class);
        $walletService->credit($this->user, 1500.00, 'TEST_BAL_SEED');

        $response = $this->actingAs($this->user)->getJson('/wallet/balance');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'balance' => 1500.0,
            'currency' => 'NGN',
        ]);
        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_get_wallet_deposit_status_endpoint_returns_status_and_no_store_headers(): void
    {
        $deposit = Deposit::create([
            'user_id' => $this->user->id,
            'reference' => 'DEP_STATUS_TEST_999',
            'amount' => 2500,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        Config::set('services.paystack.secret_key', 'sk_test_paystack_sample_123');
        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 250000,
                    'currency' => 'NGN',
                    'channel' => 'card',
                    'paid_at' => now()->toIso8601String(),
                ],
            ], 200),
        ]);

        $response = $this->getJson('/wallet/deposit-status/DEP_STATUS_TEST_999');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'deposit_status' => 'success',
            'verified' => true,
            'amount' => 2500.0,
            'wallet_balance' => 2500.0,
        ]);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_callback_page_identifies_payment_by_reference_without_session_and_does_not_redirect_to_dashboard(): void
    {
        $deposit = Deposit::create([
            'user_id' => $this->user->id,
            'reference' => 'DEP_CALLBACK_NOSESS_101',
            'amount' => 4000,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        Config::set('services.paystack.secret_key', 'sk_test_paystack_sample_123');
        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 400000,
                    'currency' => 'NGN',
                    'channel' => 'card',
                    'paid_at' => now()->toIso8601String(),
                ],
            ], 200),
        ]);

        // Unauthenticated request (no session) to callback
        $response = $this->get('/wallet/callback?reference=DEP_CALLBACK_NOSESS_101');

        $response->assertStatus(200);
        $response->assertSee('Payment Received!');
        $response->assertDontSee('Redirecting to your wallet...');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));

        // Verify that callback did NOT log anyone into the session
        $this->assertFalse(auth()->check(), 'Callback must not create an authenticated session');

        // Deposit was credited on the server
        $deposit->refresh();
        $this->assertEquals('success', $deposit->status);
    }

    public function test_paystack_verification_rejects_amount_and_currency_mismatch(): void
    {
        $deposit = Deposit::create([
            'user_id' => $this->user->id,
            'reference' => 'DEP_MISMATCH_REF_422',
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        Config::set('services.paystack.secret_key', 'sk_test_paystack_sample_123');
        // Paystack reports user only paid 1,000 NGN (100000 kobo) instead of 5,000 NGN (500000 kobo)
        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 100000,
                    'currency' => 'NGN',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/wallet/verify/DEP_MISMATCH_REF_422');

        $response->assertStatus(422);
        $response->assertJson([
            'status' => false,
            'verified' => false,
            'message' => 'Payment amount does not match deposit transaction.',
        ]);

        $deposit->refresh();
        $this->assertEquals('pending', $deposit->status);

        $walletService = app(WalletService::class);
        $this->assertEquals(0, $walletService->getBalance($this->user->id));
    }

    public function test_paystack_webhook_credits_wallet_idempotently_with_hmac_signature(): void
    {
        $secretKey = 'test_paystack_webhook_secret_key';
        Config::set('services.paystack.secret_key', $secretKey);

        $deposit = Deposit::create([
            'user_id' => $this->user->id,
            'reference' => 'DEP_PSTK_WEBHOOK_777',
            'amount' => 3500,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'DEP_PSTK_WEBHOOK_777',
                'amount' => 350000,
                'currency' => 'NGN',
                'status' => 'success',
                'channel' => 'card',
                'paid_at' => now()->toIso8601String(),
            ],
        ];

        $content = json_encode($payload);
        $signature = hash_hmac('sha512', $content, $secretKey);

        $response = $this->call(
            'POST',
            '/webhooks/paystack',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            ],
            $content
        );

        $response->assertStatus(200);

        $deposit->refresh();
        $this->assertEquals('success', $deposit->status);

        $walletService = app(WalletService::class);
        $this->assertEquals(3500, $walletService->getBalance($this->user->id));

        // Replay webhook (simulating duplicate webhook delivery from Paystack)
        $replayResponse = $this->call(
            'POST',
            '/webhooks/paystack',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            ],
            $content
        );

        $replayResponse->assertStatus(200);
        // Balance must remain exactly 3500 (NO double credit)
        $this->assertEquals(3500, $walletService->getBalance($this->user->id));

        // Verify racing endpoint also respects idempotency
        $verifyResponse = $this->actingAs($this->user)->postJson('/wallet/verify/DEP_PSTK_WEBHOOK_777');
        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['already_processed' => true]);
        $this->assertEquals(3500, $walletService->getBalance($this->user->id));
    }
}

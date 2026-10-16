<?php

namespace Tests\Feature;

use App\Application\Services\OrderService;
use App\Application\Services\PricingService;
use App\Application\Services\WalletService;
use App\Jobs\PlaceProviderOrder;
use App\Jobs\ReconcileOrder;
use App\Jobs\SyncOrder;
use App\Models\Currency;
use App\Models\NumberOrder;
use App\Models\Provider;
use App\Models\SmsMessage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SimoraAllServicesOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Currency $ngnCurrency;
    protected PricingService $pricingService;
    protected WalletService $walletService;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ngnCurrency = Currency::firstOrCreate(
            ['code' => 'NGN'],
            ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::factory()->create(['status' => 'active']);

        $this->pricingService = app(PricingService::class);
        $this->walletService = app(WalletService::class);
        $this->orderService = app(OrderService::class);

        // Ensure user wallet exists
        $this->walletService->getOrCreateUserWallet($this->user, $this->ngnCurrency);

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance' => Http::response(['data' => ['credits' => 100.0]], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr' => Http::response([
                'status' => 'success',
                'data' => [
                    '_id' => 'req_test_default',
                    'number' => '+12055550199',
                    'status' => 'ACTIVE',
                ],
            ], 200),
        ]);
    }

    /**
     * 1. Quote and duration validation per country.
     */
    public function test_quote_and_duration_validation_per_country(): void
    {
        // US: 28 days -> $12.99
        $usQuote = $this->pricingService->quote('US', 28);
        $this->assertEquals('US', $usQuote['country']);
        $this->assertEquals(28, $usQuote['duration_days']);
        $this->assertEquals(12.99, $usQuote['usd_price']);
        $this->assertGreaterThan(0, $usQuote['price_kobo']);
        $this->assertFalse($usQuote['is_new']);

        // UK: 30 days -> $6.99, 90 days -> $11.99
        $uk30 = $this->pricingService->quote('UK', 30);
        $this->assertEquals(6.99, $uk30['usd_price']);
        $uk90 = $this->pricingService->quote('UK', 90);
        $this->assertEquals(11.99, $uk90['usd_price']);

        // HK: 30 days -> $11.50, 180 days -> $22.50 (Badge: New)
        $hk30 = $this->pricingService->quote('HK', 30);
        $this->assertEquals(11.50, $hk30['usd_price']);
        $this->assertTrue($hk30['is_new']);
        $hk180 = $this->pricingService->quote('HK', 180);
        $this->assertEquals(22.50, $hk180['usd_price']);

        // ID: 30 days -> $11.99, 180 days -> $21.99 (Badge: New)
        $id30 = $this->pricingService->quote('ID', 30);
        $this->assertEquals(11.99, $id30['usd_price']);
        $this->assertTrue($id30['is_new']);
        $id180 = $this->pricingService->quote('ID', 180);
        $this->assertEquals(21.99, $id180['usd_price']);

        // AU: 30 days -> $22.99 only (Badge: New)
        $au30 = $this->pricingService->quote('AU', 30);
        $this->assertEquals(22.99, $au30['usd_price']);
        $this->assertTrue($au30['is_new']);

        // US invalid duration (e.g. 30 days instead of 28)
        $this->expectException(ValidationException::class);
        $this->pricingService->quote('US', 30);
    }

    /**
     * 2. Rejection of Canada and any unlisted country with 422.
     */
    public function test_rejection_of_canada_and_unlisted_country(): void
    {
        // Canada rejection
        try {
            $this->pricingService->quote('CA', 30);
            $this->fail('Canada should be rejected with ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('country', $e->errors());
        }

        // Unlisted country rejection (e.g. France)
        try {
            $this->pricingService->quote('FR', 30);
            $this->fail('France should be rejected with ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('country', $e->errors());
        }

        // API rejection of Canada via POST /api/v1/orders
        $this->actingAs($this->user);
        $response = $this->postJson('/api/v1/orders', [
            'country' => 'CA',
            'duration' => 30,
        ], ['Idempotency-Key' => 'test_idem_ca_1']);

        $response->assertStatus(422);
    }

    /**
     * 3. Wallet idempotency (repeated operation with same reference is a safe no-op).
     */
    public function test_wallet_idempotency_credit_debit_refund(): void
    {
        $wallet = $this->walletService->getOrCreateUserWallet($this->user, $this->ngnCurrency);

        // 1. Initial credit
        $tx1 = $this->walletService->creditKobo($this->user, 500000, 'paystack:DEP_TEST_101');
        $this->assertEquals(500000, $this->walletService->getBalanceKobo($this->user));

        // 2. Repeat exact same credit reference -> safe no-op returning existing transaction
        $tx2 = $this->walletService->creditKobo($this->user, 500000, 'paystack:DEP_TEST_101');
        $this->assertEquals($tx1->id, $tx2->id);
        $this->assertEquals(500000, $this->walletService->getBalanceKobo($this->user));

        // 3. Debit
        $d1 = $this->walletService->debitKobo($this->user, 200000, 'order:ORD_TEST_1:debit');
        $this->assertEquals(300000, $this->walletService->getBalanceKobo($this->user));

        // 4. Repeat exact same debit reference -> safe no-op
        $d2 = $this->walletService->debitKobo($this->user, 200000, 'order:ORD_TEST_1:debit');
        $this->assertEquals($d1->id, $d2->id);
        $this->assertEquals(300000, $this->walletService->getBalanceKobo($this->user));

        // 5. Refund
        $r1 = $this->walletService->refundKobo($this->user, 200000, 'order:ORD_TEST_1:refund');
        $this->assertEquals(500000, $this->walletService->getBalanceKobo($this->user));

        // 6. Repeat exact same refund reference -> safe no-op
        $r2 = $this->walletService->refundKobo($this->user, 200000, 'order:ORD_TEST_1:refund');
        $this->assertEquals($r1->id, $r2->id);
        $this->assertEquals(500000, $this->walletService->getBalanceKobo($this->user));
    }

    /**
     * 4. Double-tap with the same Idempotency-Key returns existing order and never double debits.
     */
    public function test_double_tap_with_same_idempotency_key(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance' => Http::response(['data' => ['credits' => 100.0]], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr' => Http::response([
                'status' => 'success',
                'data' => [
                    '_id' => 'req_us_1',
                    'number' => '+12055550199',
                    'status' => 'ACTIVE',
                ],
            ], 200),
        ]);

        // Fund user wallet with 100,000 NGN (10,000,000 kobo)
        $this->walletService->creditKobo($this->user, 10000000, 'paystack:DEP_FUND_1');
        $initialBalance = $this->walletService->getBalanceKobo($this->user);

        $idemKey = 'double_tap_key_xyz_1';

        // Order 1
        $order1 = $this->orderService->create($this->user, 'US', 28, $idemKey);
        $balanceAfterFirst = $this->walletService->getBalanceKobo($this->user);

        $this->assertLessThan($initialBalance, $balanceAfterFirst);

        // Order 2 with identical key
        $order2 = $this->orderService->create($this->user, 'US', 28, $idemKey);
        $balanceAfterSecond = $this->walletService->getBalanceKobo($this->user);

        // Assert exact same order returned and NO second debit occurred
        $this->assertEquals($order1->id, $order2->id);
        $this->assertEquals($balanceAfterFirst, $balanceAfterSecond);
    }

    /**
     * 5. Definitive failure refunds once.
     */
    public function test_definitive_failure_refunds_once(): void
    {
        // Use array queue to prevent afterCommit() from running PlaceProviderOrder synchronously
        config(['queue.default' => 'array']);
        Cache::forget('pvadeals_carrier_balance');

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance*' => Http::response(['data' => ['credits' => 100.0]], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr*' => Http::response([
                'status' => 400,
                'message' => 'No numbers available in this region currently',
            ], 400),
        ]);

        $this->walletService->creditKobo($this->user, 5000000, 'paystack:DEP_FUND_2');
        $initialBalance = $this->walletService->getBalanceKobo($this->user);

        $order = $this->orderService->create($this->user, 'UK', 30, 'idem_fail_1');
        $balanceAfterDebit = $this->walletService->getBalanceKobo($this->user);
        $this->assertLessThan($initialBalance, $balanceAfterDebit);

        // Run PlaceProviderOrder job
        $job = new PlaceProviderOrder($order->id);
        $job->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order->refresh();
        $this->assertEquals('failed', $order->status);

        // Assert user was refunded back to initial balance
        $balanceAfterRefund = $this->walletService->getBalanceKobo($this->user);
        $this->assertEquals($initialBalance, $balanceAfterRefund);

        // Calling fail again is an idempotent no-op (never double-refunds)
        $this->orderService->fail($order, 'Duplicate failure call');
        $this->assertEquals($initialBalance, $this->walletService->getBalanceKobo($this->user));
    }

    /**
     * 6. Ambiguous failure goes to needs_review without refund.
     */
    public function test_ambiguous_failure_goes_to_needs_review_without_refund(): void
    {
        config(['queue.default' => 'array']);
        Cache::forget('pvadeals_carrier_balance');

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance*' => Http::response(['data' => ['credits' => 100.0]], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr*' => Http::response([
                'status' => 504,
                'message' => 'Gateway Timeout',
            ], 504),
        ]);

        $this->walletService->creditKobo($this->user, 5000000, 'paystack:DEP_FUND_3');
        $initialBalance = $this->walletService->getBalanceKobo($this->user);

        $order = $this->orderService->create($this->user, 'HK', 30, 'idem_ambig_1');
        $balanceAfterDebit = $this->walletService->getBalanceKobo($this->user);

        // Run PlaceProviderOrder job
        $job = new PlaceProviderOrder($order->id);
        $job->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order->refresh();
        $this->assertEquals('needs_review', $order->status);

        // Money safety: NO refund until reconciled
        $balanceAfterJob = $this->walletService->getBalanceKobo($this->user);
        $this->assertEquals($balanceAfterDebit, $balanceAfterJob);
    }

    /**
     * 7. PENDING then delivered via SyncOrder.
     */
    public function test_pending_then_delivered_via_sync_order(): void
    {
        config(['queue.default' => 'array']);
        Cache::forget('pvadeals_carrier_balance');

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance*' => Http::response(['data' => ['credits' => 100.0]], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr*' => Http::response([
                'status' => 'success',
                'data' => [
                    '_id' => 'req_pending_99',
                    'status' => 'PENDING',
                    'number' => null,
                ],
            ], 200),
        ]);

        $this->walletService->creditKobo($this->user, 5000000, 'paystack:DEP_FUND_4');

        $order = $this->orderService->create($this->user, 'ID', 30, 'idem_pending_1');

        $job = new PlaceProviderOrder($order->id);
        $job->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order->refresh();
        $this->assertEquals('provider_pending', $order->status);
        $this->assertEquals('req_pending_99', $order->provider_request_id);

        // Next, provider fulfills the line: GET /v3/api/request/{id} returns number
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/request/req_pending_99*' => Http::response([
                'status' => 'success',
                'data' => [
                    '_id' => 'req_pending_99',
                    'status' => 'ACTIVE',
                    'number' => '+628123456789',
                ],
            ], 200),
        ]);

        $syncJob = new SyncOrder($order->id);
        $syncJob->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order->refresh();
        $this->assertEquals('active', $order->status);
        $this->assertEquals('+628123456789', $order->phone_number);
    }

    /**
     * 8. ReconcileOrder: 0 matches, 1 match, and 2 matches.
     */
    public function test_reconcile_order_outcomes(): void
    {
        config(['queue.default' => 'array']);

        $this->walletService->creditKobo($this->user, 20000000, 'paystack:DEP_FUND_5');
        $initialBalance = $this->walletService->getBalanceKobo($this->user);

        // Case A: Exactly 1 match -> links it safely
        $order1 = $this->orderService->create($this->user, 'AU', 30, 'idem_rec_1');
        $order1->update(['status' => 'needs_review']);

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/requests*' => Http::response([
                'data' => [
                    'requests' => [
                        [
                            '_id' => 'req_au_matched_1',
                            'country' => 'AU',
                            'number' => '+61412345678',
                            'status' => 'ACTIVE',
                            'createdAt' => now()->toIso8601String(),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $recJob1 = new ReconcileOrder($order1->id);
        $recJob1->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order1->refresh();
        $this->assertEquals('active', $order1->status);
        $this->assertEquals('+61412345678', $order1->phone_number);

        // Case B: 0 matches after 10 minutes -> definitive failure refunds
        $order2 = $this->orderService->create($this->user, 'UK', 30, 'idem_rec_2');
        $order2->status = 'needs_review';
        $order2->created_at = now()->subMinutes(12);
        $order2->save();

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/requests*' => Http::response([
                'data' => ['requests' => []],
            ], 200),
        ]);

        $balanceBeforeRefund = $this->walletService->getBalanceKobo($this->user);
        $recJob2 = new ReconcileOrder($order2->id);
        $recJob2->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order2->refresh();
        $this->assertEquals('failed', $order2->status);
        $this->assertEquals($balanceBeforeRefund + $order2->price_kobo, $this->walletService->getBalanceKobo($this->user));

        // Case C: Multiple matches (2 matches) -> leaves in needs_review for manual administrator review
        $order3 = $this->orderService->create($this->user, 'US', 28, 'idem_rec_3');
        $order3->update(['status' => 'needs_review']);

        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/requests*' => Http::response([
                'data' => [
                    'requests' => [
                        ['_id' => 'req_us_dup_1', 'country' => 'US', 'number' => '+12055550001', 'createdAt' => now()->toIso8601String()],
                        ['_id' => 'req_us_dup_2', 'country' => 'US', 'number' => '+12055550002', 'createdAt' => now()->toIso8601String()],
                    ],
                ],
            ], 200),
        ]);

        $recJob3 = new ReconcileOrder($order3->id);
        $recJob3->handle($this->orderService, app(\App\Infrastructure\Providers\PvaDealsClient::class));

        $order3->refresh();
        $this->assertEquals('needs_review', $order3->status); // Never guesses!
    }

    /**
     * 9. Webhook deduplication and the 503 race condition.
     */
    public function test_webhook_deduplication_and_503_race_condition(): void
    {
        $secret = config('numbers.webhook.secret', 'pvadeals_wh_sec_99348128340192830192849182301923');

        // Part A: 503 Race Condition (webhook arrives before our order write commits)
        $smsPayload = [
            'event' => 'sms_received',
            'requestId' => 'non_existent_req_999',
            'number' => '+12050009999',
            'message' => 'Your verification code is 882190',
            'receivedAt' => time(),
        ];

        $resRace = $this->postJson("/api/webhooks/pvadeals/{$secret}", $smsPayload);
        $resRace->assertStatus(503); // Tells provider to retry 5s later

        // Part B: Create the order row
        $order = NumberOrder::create([
            'user_id' => $this->user->id,
            'idempotency_key' => 'idem_wh_test_1',
            'country' => 'US',
            'duration_days' => 28,
            'usd_price' => 12.99,
            'fx_rate' => 1650.0,
            'margin_pct' => 15.0,
            'price_kobo' => 2466000,
            'status' => 'active',
            'provider_request_id' => 'req_wh_test_101',
            'phone_number' => '+12050009999',
        ]);

        $smsPayloadSuccess = [
            'event' => 'sms_received',
            'requestId' => 'req_wh_test_101',
            'number' => '+12050009999',
            'message' => 'Your WhatsApp code is 449210',
            'from' => 'WhatsApp',
            'receivedAt' => 1700000000,
        ];

        // Part C: Send webhook once
        $res1 = $this->postJson("/api/webhooks/pvadeals/{$secret}", $smsPayloadSuccess);
        $res1->assertStatus(200);

        $this->assertEquals(1, SmsMessage::where('order_id', $order->id)->count());

        // Part D: Replay exact same webhook payload -> Deduplication returns 200 without creating second SMS
        $res2 = $this->postJson("/api/webhooks/pvadeals/{$secret}", $smsPayloadSuccess);
        $res2->assertStatus(200);

        $this->assertEquals(1, SmsMessage::where('order_id', $order->id)->count());
        $this->assertEquals(1, WebhookEvent::count());
    }

    /**
     * 10. Insufficient balance returns 402 and prevents order creation.
     */
    public function test_insufficient_balance_returns_402(): void
    {
        // Credit only 1,000 NGN (100,000 kobo)
        $this->walletService->creditKobo($this->user, 100000, 'paystack:DEP_TINY_1');

        $this->actingAs($this->user);

        $response = $this->postJson('/api/v1/orders', [
            'country' => 'US',
            'duration' => 28,
        ], ['Idempotency-Key' => 'test_idem_insufficient_1']);

        $response->assertStatus(402);
        $response->assertJson([
            'message' => 'Insufficient wallet balance. Please top up your wallet to proceed.',
        ]);

        // Assert no order was created
        $this->assertEquals(0, NumberOrder::where('idempotency_key', 'test_idem_insufficient_1')->count());
    }
}

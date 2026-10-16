<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\PricingRule;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1EndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;
    protected Service $service;
    protected NumberType $numberType;
    protected Provider $provider;
    protected Currency $currency;
    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletService = app(WalletService::class);

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'api_tester@example.com'],
            ['name' => 'API Tester User', 'password' => Hash::make('password'), 'status' => 'active']
        );

        $this->country = Country::firstOrCreate(
            ['iso2' => 'US'],
            ['name' => 'United States', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD', 'is_active' => true]
        );

        $this->service = Service::firstOrCreate(
            ['code' => 'whatsapp'],
            ['name' => 'WhatsApp', 'description' => 'WhatsApp verification', 'is_active' => true]
        );

        $this->numberType = NumberType::firstOrCreate(
            ['code' => 'mobile'],
            ['name' => 'Mobile Cellular', 'is_active' => true]
        );

        $this->provider = Provider::firstOrCreate(
            ['code' => 'twilio'],
            ['name' => 'Twilio Telecom Carrier', 'type' => 'aggregator', 'priority' => 1, 'status' => 'active']
        );

        PricingRule::firstOrCreate(
            [
                'country_id' => $this->country->id,
                'service_id' => $this->service->id,
            ],
            [
                'purchase_price' => 0.40,
                'selling_price' => 0.85,
                'currency_code' => 'USD',
                'priority' => 1,
                'is_active' => true,
            ]
        );
    }

    public function test_can_list_countries_and_services_publicly(): void
    {
        $resCountries = $this->getJson('/api/v1/countries');
        $resCountries->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'United States', 'iso2' => 'US']);

        $resServices = $this->getJson('/api/v1/services');
        $resServices->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['code' => 'whatsapp', 'name' => 'WhatsApp']);
    }

    public function test_can_query_pricing(): void
    {
        $res = $this->getJson("/api/v1/pricing?country_id={$this->country->id}&service_id={$this->service->id}&currency=USD");
        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.price', 0.85)
            ->assertJsonPath('data.currency', 'USD');
    }

    public function test_wallet_balance_and_deposit_endpoints(): void
    {
        Sanctum::actingAs($this->user);

        // Check initial balance
        $res = $this->getJson('/api/v1/wallet/balance');
        $res->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.balance', 0);

        // Deposit funds
        $depRes = $this->postJson('/api/v1/wallet/deposit', [
            'amount' => 25.50,
        ]);
        $depRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('wallet.balance', 25.50)
            ->assertJsonPath('wallet.available_balance', 25.50);

        // Check transaction audit history
        $txRes = $this->getJson('/api/v1/wallet/transactions');
        $txRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_full_verification_flow_via_api(): void
    {
        Sanctum::actingAs($this->user);

        // 1. Credit wallet
        $this->walletService->credit($this->user, 10.00, 'API_TEST_CREDIT');

        // 2. Request verification number
        $reqRes = $this->postJson('/api/v1/verifications/request', [
            'country_id' => $this->country->id,
            'service_id' => $this->service->id,
        ]);

        $reqRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'active');

        $sessionId = $reqRes->json('data.id');
        $allocatedNumber = $reqRes->json('data.phone_number.e164');

        $this->assertNotEmpty($allocatedNumber);

        // 3. Poll session status
        $pollRes = $this->getJson("/api/v1/verifications/{$sessionId}");
        $pollRes->assertStatus(200)
            ->assertJsonPath('data.id', $sessionId)
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.verification_code', null);

        // 4. Ingest incoming SMS webhook (e.g. Twilio carrier callback)
        $webhookRes = $this->post('/api/v1/webhooks/providers/twilio', [
            'To' => $allocatedNumber,
            'From' => 'WhatsApp',
            'Body' => 'Your WhatsApp code is 654-321. Do not share it.',
            'MessageSid' => 'SM_WEBHOOK_TEST_101',
        ]);
        $webhookRes->assertStatus(200);

        // 5. Poll session again -> should now be completed with OTP
        $pollCompleted = $this->getJson("/api/v1/verifications/{$sessionId}");
        $pollCompleted->assertStatus(200)
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.verification_code', '654321');

        // Check wallet balance was captured
        $walletRes = $this->getJson('/api/v1/wallet/balance');
        $walletRes->assertJsonPath('data.balance', 9.15)
            ->assertJsonPath('data.available_balance', 9.15);
    }

    public function test_can_cancel_session_via_api(): void
    {
        Sanctum::actingAs($this->user);

        $this->walletService->credit($this->user, 10.00, 'API_TEST_CANCEL');

        $reqRes = $this->postJson('/api/v1/verifications/request', [
            'country_id' => $this->country->id,
            'service_id' => $this->service->id,
        ]);

        $sessionId = $reqRes->json('data.id');

        // Cancel
        $cancelRes = $this->postJson("/api/v1/verifications/{$sessionId}/cancel", [
            'reason' => 'user_cancelled',
        ]);

        $cancelRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');

        // Check wallet hold was released
        $walletRes = $this->getJson('/api/v1/wallet/balance');
        $this->assertEquals(10.00, (float) $walletRes->json('data.balance'));
        $this->assertEquals(10.00, (float) $walletRes->json('data.available_balance'));
    }
}

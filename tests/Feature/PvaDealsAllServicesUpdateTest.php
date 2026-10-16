<?php

namespace Tests\Feature;

use App\Application\Services\NumberAllocationService;
use App\Application\Services\WalletService;
use App\Domain\Number\Exceptions\NumberUnavailableException;
use App\Domain\Wallet\Exceptions\InsufficientBalanceException;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PhoneNumber;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Models\VerificationSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PvaDealsAllServicesUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Currency $usdCurrency;
    protected Currency $ngnCurrency;
    protected Provider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usdCurrency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->ngnCurrency = Currency::firstOrCreate(
            ['code' => 'NGN'],
            ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::factory()->create(['status' => 'active']);

        $this->provider = Provider::updateOrCreate(
            ['code' => 'pvadeals'],
            ['name' => 'PVADeals v3 API Gateway', 'type' => 'aggregator', 'status' => 'active', 'priority' => 1]
        );
    }

    public function test_can_purchase_uk_30_and_90_day_plans(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr' => Http::sequence()
                ->push([
                    'status' => 'success',
                    'data' => [
                        '_id' => 'req_uk_30',
                        'number' => '+447911123456',
                        'serviceId' => 'ALL_SERVICES',
                        'country' => 'UK',
                        'duration' => 30,
                        'amount' => 6.99,
                    ],
                ], 200)
                ->push([
                    'status' => 'success',
                    'data' => [
                        '_id' => 'req_uk_90',
                        'number' => '+447911987654',
                        'serviceId' => 'ALL_SERVICES',
                        'country' => 'UK',
                        'duration' => 90,
                        'amount' => 11.99,
                    ],
                ], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        // 1. UK 30 days ($6.99)
        $res30 = $client->purchaseLtr('ALL_SERVICES', 30, 'UK');
        $this->assertTrue($res30['status'] === 'success' || ($res30['success'] ?? false));
        $this->assertEquals('+447911123456', $res30['data']['number']);
        $this->assertEquals(30, $res30['data']['duration']);

        // 2. UK 90 days ($11.99)
        $res90 = $client->purchaseLtr('ALL_SERVICES', 90, 'UK');
        $this->assertTrue($res90['status'] === 'success' || ($res90['success'] ?? false));
        $this->assertEquals('+447911987654', $res90['data']['number']);
        $this->assertEquals(90, $res90['data']['duration']);
    }

    public function test_can_purchase_hk_id_au_plans(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/purchase-ltr' => Http::sequence()
                ->push(['status' => 'success', 'data' => ['_id' => 'req_hk_30', 'number' => '+85291234567', 'country' => 'HK', 'duration' => 30]], 200)
                ->push(['status' => 'success', 'data' => ['_id' => 'req_hk_180', 'number' => '+85291234568', 'country' => 'HK', 'duration' => 180]], 200)
                ->push(['status' => 'success', 'data' => ['_id' => 'req_id_30', 'number' => '+62812345678', 'country' => 'ID', 'duration' => 30]], 200)
                ->push(['status' => 'success', 'data' => ['_id' => 'req_id_180', 'number' => '+62812345679', 'country' => 'ID', 'duration' => 180]], 200)
                ->push(['status' => 'success', 'data' => ['_id' => 'req_au_30', 'number' => '+61412345678', 'country' => 'AU', 'duration' => 30]], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        // HK (30 & 180)
        $hk30 = $client->purchaseLtr('ALL_SERVICES', 30, 'HK');
        $this->assertEquals('+85291234567', $hk30['data']['number']);
        $hk180 = $client->purchaseLtr('ALL_SERVICES', 180, 'HK');
        $this->assertEquals('+85291234568', $hk180['data']['number']);

        // ID (30 & 180)
        $id30 = $client->purchaseLtr('ALL_SERVICES', 30, 'ID');
        $this->assertEquals('+62812345678', $id30['data']['number']);
        $id180 = $client->purchaseLtr('ALL_SERVICES', 180, 'ID');
        $this->assertEquals('+62812345679', $id180['data']['number']);

        // AU (30 only)
        $au30 = $client->purchaseLtr('ALL_SERVICES', 30, 'AU');
        $this->assertEquals('+61412345678', $au30['data']['number']);
    }

    public function test_async_pending_delivery_handling(): void
    {
        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        // Case 1: Response returns status PENDING with no assigned number
        $pendingResult = [
            'status' => 'success',
            'data' => [
                '_id' => 'req_pending_101',
                'status' => 'PENDING',
                'serviceId' => 'ALL_SERVICES',
                'country' => 'UK',
                'number' => null,
            ],
        ];

        $this->assertTrue($client->isPendingDelivery($pendingResult));

        // Case 2: Number is confirmed and available
        $readyResult = [
            'status' => 'success',
            'data' => [
                '_id' => 'req_ready_102',
                'status' => 'ACTIVE',
                'serviceId' => 'ALL_SERVICES',
                'number' => '+447911000111',
            ],
        ];

        $this->assertFalse($client->isPendingDelivery($readyResult));
    }

    public function test_invalid_country_or_duration_rejected_with_422(): void
    {
        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        // 1. Invalid country (FR is not in ALL_SERVICES allowed markets)
        $invalidCountry = $client->purchaseLtr('ALL_SERVICES', 30, 'FR');
        $this->assertEquals(422, $invalidCountry['status']);
        $this->assertFalse($invalidCountry['success']);
        $this->assertStringContainsString('not supported', $invalidCountry['message']);

        // 2. US duration 30 (must be 28)
        $invalidUsDuration = $client->purchaseLtr('ALL_SERVICES', 30, 'US');
        $this->assertEquals(422, $invalidUsDuration['status']);
        $this->assertFalse($invalidUsDuration['success']);
        $this->assertStringContainsString('not valid', $invalidUsDuration['message']);

        // 3. Canada is not offered - rejected at country level
        $invalidCa = $client->purchaseLtr('ALL_SERVICES', 30, 'CA');
        $this->assertEquals(422, $invalidCa['status']);
        $this->assertFalse($invalidCa['success']);
        $this->assertStringContainsString('not supported', $invalidCa['message']);

        // 4. AU duration 90 (Australia only supports 30)
        $invalidAuDuration = $client->purchaseLtr('ALL_SERVICES', 90, 'AU');
        $this->assertEquals(422, $invalidAuDuration['status']);
        $this->assertFalse($invalidAuDuration['success']);
        $this->assertStringContainsString('not valid', $invalidAuDuration['message']);
    }

    public function test_money_safety_insufficient_wallet_balance_blocks_order(): void
    {
        $walletService = app(WalletService::class);
        $allocationService = app(NumberAllocationService::class);

        $ukCountry = Country::firstOrCreate(
            ['iso2' => 'UK'],
            ['name' => 'United Kingdom', 'iso3' => 'GBR', 'dial_code' => '+44', 'currency_code' => 'GBP', 'is_active' => true]
        );

        $allServices = Service::firstOrCreate(
            ['code' => 'all-services'],
            ['name' => 'All services', 'is_active' => true]
        );

        // Credit only $1.00 when UK 30 days is $6.99 + margin
        $walletService->credit($this->user, 1.00, 'TINY_CREDIT');

        $this->expectException(InsufficientBalanceException::class);

        $allocationService->allocateVerificationNumber(
            $this->user,
            $ukCountry,
            $allServices,
            null,
            'USD',
            30
        );
    }

    public function test_renewal_webhook_updates_stored_request_id(): void
    {
        $numType = \App\Models\NumberType::firstOrCreate(['code' => 'mobile'], ['name' => 'Mobile']);
        $country = Country::firstOrCreate(['iso2' => 'UK'], ['name' => 'UK', 'iso3' => 'GBR', 'dial_code' => '+44', 'currency_code' => 'GBP', 'is_active' => true]);
        $service = Service::firstOrCreate(['code' => 'all-services'], ['name' => 'All services', 'is_active' => true]);

        $phone = PhoneNumber::create([
            'provider_id' => $this->provider->id,
            'country_id' => $country->id,
            'number_type_id' => $numType->id,
            'phone_number' => '+447911223344',
            'provider_number_id' => 'old_req_123',
            'status' => 'reserved',
        ]);

        $order = \App\Models\Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD_TEST_REN',
            'idempotency_key' => 'IDEM_TEST_REN_1',
            'status' => 'active',
            'subtotal' => 6.99,
            'total_amount' => 6.99,
            'currency_code' => 'USD',
        ]);

        $orderItem = \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'phone_number_id' => $phone->id,
            'service_id' => $service->id,
            'country_id' => $country->id,
            'number_type_id' => $numType->id,
            'quantity' => 1,
            'unit_price' => 6.99,
            'total_price' => 6.99,
        ]);

        $session = VerificationSession::create([
            'user_id' => $this->user->id,
            'order_item_id' => $orderItem->id,
            'phone_number_id' => $phone->id,
            'service_id' => $service->id,
            'status' => 'active',
            'expires_at' => now()->addDays(5),
        ]);

        $webhookPayload = [
            'event' => 'number_renewed',
            'oldRequestId' => 'old_req_123',
            'requestId' => 'new_req_456',
            'serviceId' => 'ALL_SERVICES',
            'serviceName' => 'All services',
            'amount' => 6.99,
            'newExpiresAt' => now()->addDays(35)->toIso8601String(),
        ];

        $response = $this->postJson('/api/v1/webhooks/providers/pvadeals', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'event' => 'number_renewed',
        ]);

        $phone->refresh();
        $this->assertEquals('new_req_456', $phone->provider_number_id);
    }
}

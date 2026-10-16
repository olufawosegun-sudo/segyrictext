<?php

namespace Tests\Feature;

use App\Domain\DTOs\IncomingSmsDTO;
use App\Infrastructure\Providers\PvaDealsAdapter;
use App\Infrastructure\Providers\PvaDealsClient;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\PhoneNumber;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Models\VerificationSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PvaDealsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;
    protected Service $service;
    protected NumberType $numberType;
    protected Provider $provider;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->country = Country::firstOrCreate(
            ['iso2' => 'US'],
            ['name' => 'United States', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD']
        );

        $this->service = Service::firstOrCreate(
            ['code' => 'whatsapp'],
            ['name' => 'WhatsApp', 'is_active' => true]
        );

        $this->numberType = NumberType::firstOrCreate(
            ['code' => 'mobile'],
            ['name' => 'Mobile Cellular']
        );

        $this->provider = Provider::updateOrCreate(
            ['code' => 'pvadeals'],
            ['name' => 'PVADeals v3 API Gateway', 'type' => 'aggregator', 'status' => 'active', 'priority' => 1]
        );
    }

    public function test_pvadeals_client_handles_health_and_version(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/health' => Http::response([
                'status' => 'operational',
                'version' => '1.6.0',
                'timestamp' => '2026-05-31T14:22:01.123Z',
            ], 200),
            'https://prod-v3.pvadeals.com/v3/api/version' => Http::response([
                'status' => 'ok',
                'version' => '1.6.0',
            ], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        $health = $client->checkHealth();
        $this->assertEquals('operational', $health['status']);
        $this->assertEquals('1.6.0', $health['version']);

        $version = $client->getVersion();
        $this->assertEquals('ok', $version['status']);
        $this->assertEquals('1.6.0', $version['version']);
    }

    public function test_pvadeals_client_balance_and_services(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/balance' => Http::response([
                'success' => true,
                'data' => ['credits' => 75.50],
                'message' => 'Balance retrieved successfully',
            ], 200),
            'https://prod-v3.pvadeals.com/v3/api/services/all' => Http::response([
                'success' => true,
                'data' => [
                    'services' => [
                        [
                            '_id' => 'pva_srv_1',
                            'name' => 'WhatsApp',
                            'country' => 'USA',
                            'STRprice' => 0.20,
                            'STRstock' => 50,
                            'LTRstock' => 10,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');

        $this->assertEquals(75.50, $client->getBalance());

        $services = $client->getAllServices(true);
        $this->assertCount(1, $services);
        $this->assertEquals('pva_srv_1', $services[0]['_id']);
    }

    public function test_pvadeals_webhook_ingests_sms_and_completes_session(): void
    {
        \App\Models\PricingRule::firstOrCreate(
            [
                'country_id' => $this->country->id,
                'service_id' => $this->service->id,
            ],
            [
                'purchase_price' => 0.20,
                'selling_price' => 0.85,
                'currency_code' => 'USD',
                'priority' => 1,
                'is_active' => true,
            ]
        );

        $walletService = app(\App\Application\Services\WalletService::class);
        $walletService->credit($this->user, 5000.00, 'TEST_CREDIT');

        $allocationService = app(\App\Application\Services\NumberAllocationService::class);
        $session = $allocationService->allocateVerificationNumber(
            $this->user,
            $this->country,
            $this->service,
            $this->numberType
        );

        $testPhone = $session->phoneNumber->phone_number;

        $webhookPayload = [
            'event' => 'sms_received',
            'timestamp' => '2026-01-28T22:57:35.001Z',
            'requestId' => $session->phoneNumber->provider_number_id,
            'serviceId' => '697139f7fe5460ddc2f27214',
            'number' => $testPhone,
            'message' => 'Your WhatsApp code is: 481-902. Keep this confidential.',
        ];

        $response = $this->postJson('/api/v1/webhooks/providers/pvadeals', $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'queued',
        ]);

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals('481902', $session->verification_code);
    }

    public function test_pvadeals_webhook_acknowledges_lifecycle_events(): void
    {
        $events = ['number_purchased', 'number_flagged', 'number_reused', 'sms_blocked'];

        foreach ($events as $event) {
            $payload = [
                'event' => $event,
                'timestamp' => '2026-01-28T22:42:26.452Z',
                'requestId' => '697a90d25ef1873ef44f48bc',
                'serviceId' => '697139f7fe5460ddc2f27214',
                'number' => '+13130001234',
                'message' => '',
            ];

            $response = $this->postJson('/api/v1/webhooks/providers/pvadeals', $payload);

            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
                'event' => $event,
            ]);
        }
    }

    public function test_client_supports_cursor_pagination_for_get_requests(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/requests*' => Http::response([
                'status' => 'success',
                'data' => [
                    'data' => [
                        'pageInfo' => [
                            'hasNextPage' => true,
                            'endCursor' => 'cur_123',
                        ],
                        'edges' => [
                            [
                                'cursor' => 'cur_1',
                                'node' => [
                                    '_id' => 'req_101',
                                    'number' => '+13135550100',
                                    'status' => 'ACTIVE',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');
        $raw = $client->getRequests(10);
        $this->assertEquals('success', $raw['status']);

        $nodes = $client->getRequestNodes(10);
        $this->assertCount(1, $nodes['nodes']);
        $this->assertEquals('+13135550100', $nodes['nodes'][0]['number']);
        $this->assertEquals('cur_1', $nodes['cursors'][0]);
    }

    public function test_client_supports_inventory_and_purchase_with_state(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/inventory*' => Http::response([
                'status' => 'success',
                'data' => [
                    'stock' => 150,
                    'states' => [
                        ['state' => 'CA', 'stock' => 50],
                        ['state' => 'TX', 'stock' => 45],
                    ],
                ],
            ], 200),
            'https://prod-v3.pvadeals.com/v3/api/purchase' => Http::response([
                'status' => 'success',
                'data' => [
                    '_id' => 'req_state_1',
                    'number' => '+14155550199',
                    'serviceId' => 'pva_srv_1',
                    'state' => 'CA',
                    'expiresAt' => '2026-05-31T15:00:00Z',
                ],
            ], 200),
        ]);

        $client = new PvaDealsClient('API-testkey', 'https://prod-v3.pvadeals.com/v3/api');
        $inventory = $client->getInventory('pva_srv_1', 'STR');

        $this->assertEquals(150, $inventory['stock']);
        $this->assertCount(2, $inventory['states']);

        $purchase = $client->purchaseNumber('pva_srv_1', 'STR', 'USA', 'CA');
        $reqData = $purchase['data'] ?? $purchase;
        $this->assertEquals('req_state_1', $reqData['_id']);
        $this->assertEquals('+14155550199', $reqData['number']);
    }

    public function test_catalog_inventory_route_returns_json(): void
    {
        Http::fake([
            'https://prod-v3.pvadeals.com/v3/api/inventory*' => Http::response([
                'status' => 'success',
                'data' => [
                    'stock' => 75,
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/pvadeals/inventory?serviceId=srv_1&type=STR&state=CA');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.stock', 75);
    }
}

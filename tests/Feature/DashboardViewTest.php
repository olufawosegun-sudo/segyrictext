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
use Tests\TestCase;

class DashboardViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->walletService = app(WalletService::class);

        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'web_tester@example.com'],
            ['name' => 'Web Tester User', 'password' => Hash::make('password'), 'status' => 'active']
        );

        $country = Country::firstOrCreate(
            ['iso2' => 'US'],
            ['name' => 'United States', 'iso3' => 'USA', 'dial_code' => '+1', 'currency_code' => 'USD', 'is_active' => true]
        );

        $service = Service::firstOrCreate(
            ['code' => 'whatsapp'],
            ['name' => 'WhatsApp', 'description' => 'WhatsApp verification', 'is_active' => true]
        );

        PricingRule::firstOrCreate(
            [
                'country_id' => $country->id,
                'service_id' => $service->id,
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

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $res = $this->get('/dashboard');
        $res->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_sms_dashboard(): void
    {
        // Credit wallet with $50.00
        $this->walletService->credit($this->user, 50.00, 'TEST_WEB_INITIAL_FUNDS');

        $res = $this->actingAs($this->user)->get('/dashboard');

        $res->assertStatus(200)
            ->assertSee('SMS Verification Console')
            ->assertSee('50.00')
            ->assertSee('Get a New Virtual Number')
            ->assertSee('Carrier Inbound SMS Simulator')
            ->assertSee('WhatsApp')
            ->assertSee('United States');
    }
}

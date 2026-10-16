<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Models\Country;
use App\Models\Currency;
use App\Models\NumberType;
use App\Models\PricingRule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileDashboardTest extends TestCase
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
            ['email' => 'mobile_tester@example.com'],
            [
                'name' => 'Mobile Tester',
                'first_name' => 'Mobile',
                'last_name' => 'Tester',
                'phone' => '+12345678901',
                'password' => Hash::make('password'),
                'status' => 'active'
            ]
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
            ['country_id' => $country->id, 'service_id' => $service->id],
            ['purchase_price' => 0.40, 'selling_price' => 0.85, 'currency_code' => 'USD', 'priority' => 1, 'is_active' => true]
        );

        $this->walletService->credit($this->user, 35.00, 'TEST_CREDIT');
    }

    public function test_native_dashboard_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/dashboard');
        $res->assertStatus(200)
            ->assertSee('Welcome, ' . $this->user->name)
            ->assertSee('35.00');
    }

    public function test_mobile_home_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/mobile');
        $res->assertStatus(200)
            ->assertSee('Available Balance')
            ->assertSee('35.00');
    }

    public function test_mobile_get_number_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/mobile/get-number');
        $res->assertStatus(200)
            ->assertSee('Country')
            ->assertSee('United States');
    }

    public function test_mobile_sessions_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/mobile/sessions');
        $res->assertStatus(200)
            ->assertSee('Verifications')
            ->assertSee('History');
    }

    public function test_mobile_wallet_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/mobile/wallet');
        $res->assertStatus(200)
            ->assertSee('Available Balance')
            ->assertSee('35.00')
            ->assertSee('Transaction Ledger');
    }

    public function test_mobile_settings_renders_successfully(): void
    {
        $res = $this->actingAs($this->user)->get('/mobile/settings');
        $res->assertStatus(200)
            ->assertSee('Mobile Tester')
            ->assertSee('Platform Architecture')
            ->assertSee('NativePHP Mobile');
    }
}

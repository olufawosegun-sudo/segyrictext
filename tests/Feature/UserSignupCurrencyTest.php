<?php

namespace Tests\Feature;

use App\Application\Services\WalletService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSignupCurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = app(WalletService::class);
    }

    public function test_user_signing_up_from_nigeria_gets_ngn_wallet(): void
    {
        $user = User::create([
            'name' => 'Naija User',
            'email' => 'naija@example.com',
            'password' => bcrypt('password'),
            'country_code' => 'NG',
        ]);

        $wallet = $this->walletService->getOrCreateUserWallet($user);

        $this->assertEquals('NGN', $wallet->currency->code);
        $this->assertEquals('₦', $wallet->currency->symbol);
    }

    public function test_user_signing_up_from_kenya_gets_kes_wallet(): void
    {
        $user = User::create([
            'name' => 'Kenya User',
            'email' => 'kenya@example.com',
            'password' => bcrypt('password'),
            'country_code' => 'KE',
        ]);

        $wallet = $this->walletService->getOrCreateUserWallet($user);

        $this->assertEquals('KES', $wallet->currency->code);
        $this->assertEquals('KSh', $wallet->currency->symbol);
    }

    public function test_user_signing_up_from_ghana_gets_ghs_wallet(): void
    {
        $user = User::create([
            'name' => 'Ghana User',
            'email' => 'ghana@example.com',
            'password' => bcrypt('password'),
            'country_code' => 'GH',
        ]);

        $wallet = $this->walletService->getOrCreateUserWallet($user);

        $this->assertEquals('GHS', $wallet->currency->code);
        $this->assertEquals('GH₵', $wallet->currency->symbol);
    }

    public function test_user_signing_up_from_south_africa_gets_zar_wallet(): void
    {
        $user = User::create([
            'name' => 'SA User',
            'email' => 'sa@example.com',
            'password' => bcrypt('password'),
            'country_code' => 'ZA',
        ]);

        $wallet = $this->walletService->getOrCreateUserWallet($user);

        $this->assertEquals('ZAR', $wallet->currency->code);
        $this->assertEquals('R', $wallet->currency->symbol);
    }

    public function test_api_registration_sets_signup_country_and_wallet_currency(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'API Registered User',
            'email' => 'apiregister@example.com',
            'password' => 'password123',
            'country_code' => 'NG',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $user = User::where('email', 'apiregister@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('NG', $user->country_code);

        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $this->assertEquals('NGN', $wallet->currency->code);
    }

    public function test_web_registration_sets_signup_country_and_wallet_currency(): void
    {
        $response = $this->post('/register', [
            'name' => 'Web Registered User',
            'email' => 'webregister@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'country_code' => 'KE',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'webregister@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('KE', $user->country_code);

        $wallet = $this->walletService->getOrCreateUserWallet($user);
        $this->assertEquals('KES', $wallet->currency->code);
    }
}

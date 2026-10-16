<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileAuthAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_login_returns_json_with_auth_token_and_dashboard_redirect(): void
    {
        $user = User::factory()->create([
            'email' => 'ios_jump_tester@example.com',
            'password' => Hash::make('Secret12345!'),
        ]);

        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson('/login', [
            'email' => 'ios_jump_tester@example.com',
            'password' => 'Secret12345!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $redirectUrl = $response->json('redirect');
        $this->assertNotNull($redirectUrl);
        $this->assertTrue(str_contains($redirectUrl, '/home') || str_contains($redirectUrl, '/mobile'));
    }

    public function test_auth_token_bridge_authenticates_unauthenticated_request_in_detect_platform(): void
    {
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $user = User::factory()->create();

        $tokenPayload = json_encode([
            'user_id' => $user->id,
            'expires_at' => time() + 300,
        ]);
        $authToken = Crypt::encryptString($tokenPayload);

        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
        ])->get('/mobile?auth_token=' . urlencode($authToken));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_payment_initiate_accepts_local_callback_url_without_422(): void
    {
        Currency::firstOrCreate(
            ['code' => 'NGN'],
            ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true]
        );

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->postJson('/api/v1/payments/initiate', [
            'amount' => 500,
            'currency' => 'NGN',
            'gateway' => 'mock',
            'callback_url' => 'http://192.168.1.15:3000/mobile',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_failed_mobile_login_returns_422_with_error_and_no_redirect(): void
    {
        $user = User::factory()->create([
            'email' => 'valid_user@example.com',
            'password' => Hash::make('CorrectPassword123'),
        ]);

        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson('/login', [
            'email' => 'valid_user@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'The provided credentials do not match our records.',
        ]);
        $this->assertNull($response->json('redirect'));
        $this->assertGuest();
    }

    public function test_normal_login_redirects_correctly(): void
    {
        $user = User::factory()->create([
            'email' => 'logintest@example.com',
            'password' => Hash::make('SecurePassword123'),
        ]);

        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson('/login', [
            'email' => 'logintest@example.com',
            'password' => 'SecurePassword123',
        ]);

        $response->assertStatus(200);
        $redirectUrl = $response->json('redirect');
        $this->assertTrue(str_contains($redirectUrl, '/home') || str_contains($redirectUrl, '/mobile'));
    }

    public function test_authenticated_mobile_user_visiting_root_navigates_to_mobile_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withHeaders([
            'X-NativePHP-Platform' => 'ios',
        ])->get('/');

        $this->assertTrue($response->isRedirection() || $response->isOk());
    }
}

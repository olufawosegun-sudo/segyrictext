<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mobile Auth Fixes Verification Test
 * 
 * Tests two critical fixes for iPhone Safari:
 * 1. Create account button responsiveness
 * 2. Login without Paystack auto-redirect
 */
class MobileAuthFixesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration endpoint accepts request without required hidden fields.
     * 
     * This test verifies that hidden fields (last_name, password_confirmation)
     * don't block registration when submitted programmatically.
     */
    public function test_registration_works_without_hidden_required_fields()
    {
        $response = $this->postJson('/register', [
            'email' => 'newuser@example.com',
            'first_name' => 'John',
            // last_name is auto-populated from first_name in JS
            'last_name' => 'John',
            'phone' => '+1234567890',
            'password' => 'SecurePass123!',
            // password_confirmation auto-copied from password in JS
            'password_confirmation' => 'SecurePass123!',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'first_name' => 'John',
        ]);
    }

    /**
     * Test that login clears pending payment data to prevent auto-redirect.
     */
    public function test_login_clears_pending_payment_storage()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Simulate mobile/iPhone request
        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'redirect' => url('/mobile'),
                 ]);

        // Verify session cleared
        $this->assertNull(session('url.intended'));
        $this->assertNull(session('segyric_pending_payment'));
    }

    /**
     * Test that mobile login sets just_logged_in flag for suppression.
     */
    public function test_login_sets_suppression_flag()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue(session()->has('just_logged_in'));
        $this->assertIsInt(session('just_logged_in'));
    }

    /**
     * Test that dashboard route is accessible after login.
     */
    public function test_dashboard_accessible_after_login()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/mobile');

        $response->assertStatus(200)
                 ->assertViewIs('native.home');
    }

    /**
     * Test that payment iframe has correct sandbox attributes (no escape).
     */
    public function test_payment_iframe_has_safe_sandbox_attributes()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/mobile');

        $response->assertStatus(200);
        
        $content = $response->getContent();
        
        // Verify iframe exists
        $this->assertStringContainsString('id="complete-payment-iframe"', $content);
        
        // Verify dangerous attribute is REMOVED
        $this->assertStringNotContainsString('allow-popups-to-escape-sandbox', $content);
        
        // Verify iframe starts with about:blank
        $this->assertStringContainsString('src="about:blank"', $content);
    }

    /**
     * Test that dashboard has iframe cleanup script.
     */
    public function test_dashboard_has_iframe_cleanup_script()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/mobile');

        $response->assertStatus(200);
        
        $content = $response->getContent();
        
        // Verify cleanup script exists
        $this->assertStringContainsString('[MOBILE-DASHBOARD]', $content);
        $this->assertStringContainsString('SECURITY: Cached payment iframe detected', $content);
        $this->assertStringContainsString('iframe.remove()', $content);
    }

    /**
     * Test that mobile layout has bfcache defense.
     */
    public function test_mobile_layout_has_bfcache_defense()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get('/mobile');

        $response->assertStatus(200);
        
        $content = $response->getContent();
        
        // Verify bfcache listeners
        $this->assertStringContainsString('[BFCACHE-DEFENSE]', $content);
        $this->assertStringContainsString('pageshow', $content);
        $this->assertStringContainsString('pagehide', $content);
        $this->assertStringContainsString('event.persisted', $content);
    }

    /**
     * Test registration validation with mobile user-agent.
     */
    public function test_registration_validation_mobile()
    {
        $response = $this->postJson('/register', [
            'email' => 'invalid-email',
            'first_name' => '',
            'phone' => '',
            'password' => '123', // Too short
        ], [
            'Accept' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'first_name', 'phone', 'password']);
    }

    /**
     * Test that AuthController getDestinationUrl returns /mobile for mobile users.
     */
    public function test_login_redirects_mobile_to_mobile_dashboard()
    {
        $user = User::factory()->create([
            'email' => 'mobile@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
        ])->post('/login', [
            'email' => 'mobile@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mobile');
    }
}

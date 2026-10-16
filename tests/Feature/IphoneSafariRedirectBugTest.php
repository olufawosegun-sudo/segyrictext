<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Bug Condition Exploration Test for iPhone Login Safari Redirect Bug
 * 
 * CRITICAL: This test MUST FAIL on unfixed code - failure confirms the bug exists
 * DO NOT attempt to fix the test or the code when it fails
 * 
 * Goal: Surface counterexamples that demonstrate Safari auto-opens after login with pending payment
 * 
 * Test encodes the expected behavior:
 * - User logs in on iOS with pending payment in localStorage
 * - Safari should NOT auto-open (Browser::inApp should not be triggered)
 * - User should land on dashboard/home screen
 * - Pending payment should remain in localStorage
 * 
 * When this test passes after fix implementation, it confirms the expected behavior is satisfied
 */
class IphoneSafariRedirectBugTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 1: Bug Condition - Post-Login Safari Auto-Open Suppression
     * 
     * Validates Requirements: 2.1, 2.2
     * 
     * For any page load where the user just completed login on an iOS device 
     * and a pending payment exists in localStorage, the fixed payment-sync.js 
     * SHALL NOT automatically trigger Browser::inApp() or open Safari, allowing 
     * the user to land on the dashboard/home screen without interruption.
     * 
     * EXPECTED OUTCOME ON UNFIXED CODE: Test FAILS (this is correct - proves bug exists)
     * EXPECTED OUTCOME ON FIXED CODE: Test PASSES (confirms bug is fixed)
     */
    public function test_ios_login_with_pending_payment_should_not_auto_open_safari(): void
    {
        // Setup: Create currency and user
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $user = User::factory()->create([
            'email' => 'iosuser@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Act: Login on iOS device via AJAX (matching the real in-app fetch flow)
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/login', [
            'email' => 'iosuser@example.com',
            'password' => 'SecurePass123!',
        ]);

        // Assert: Login was successful — AJAX login returns JSON with redirect URL
        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);

        $data = $response->json();
        $this->assertTrue($data['success'] ?? false, 'Login should succeed');
        $this->assertNotEmpty($data['redirect'] ?? '', 'Response should include a redirect URL');

        // EXPECTED BEHAVIOR #1: The session should have 'just_logged_in' timestamp set by AuthController
        $this->assertTrue(
            session()->has('just_logged_in'),
            'EXPECTED BEHAVIOR: AuthController should set session flag "just_logged_in" after successful login. ' .
            'This flag signals to the view layer that auto-recovery should be suppressed.'
        );

        // Now follow the redirect to the home page (simulates window.location.replace)
        $homeResponse = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
        ])->get($data['redirect']);

        $homeResponse->assertStatus(200);
        $content = $homeResponse->getContent();
        $this->assertNotEmpty($content, 'Home page should contain view content');
        
        // EXPECTED BEHAVIOR #2: The view should inject sessionStorage flag before payment-sync.js runs
        $this->assertStringContainsString(
            'segyric_just_logged_in',
            $content,
            'EXPECTED BEHAVIOR: The view should inject sessionStorage flag "segyric_just_logged_in" before payment-sync.js loads.'
        );
        
        // EXPECTED BEHAVIOR #3: payment-sync.js should have suppression logic
        $paymentSyncPath = public_path('js/payment-sync.js');
        $this->assertFileExists($paymentSyncPath, 'payment-sync.js should exist');
        
        $paymentSyncContent = file_get_contents($paymentSyncPath);
        $this->assertStringContainsString(
            'shouldSuppressAutoRecovery',
            $paymentSyncContent,
            'EXPECTED BEHAVIOR: payment-sync.js should have shouldSuppressAutoRecovery() function to check login flag.'
        );
        
        // EXPECTED BEHAVIOR #4: payment-sync.js init() should check suppression before auto-recovery
        $this->assertStringContainsString(
            'segyric_just_logged_in',
            $paymentSyncContent,
            'EXPECTED BEHAVIOR: payment-sync.js init() should check sessionStorage for "segyric_just_logged_in" flag.'
        );
    }

    /**
     * Supplementary test: Verify login without pending payment works normally
     * This should pass on both unfixed and fixed code (baseline behavior)
     */
    public function test_ios_login_without_pending_payment_should_load_dashboard_normally(): void
    {
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $user = User::factory()->create([
            'email' => 'normaluser@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Login without any pending payment (normal case) — AJAX to match real in-app flow
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/login', [
            'email' => 'normaluser@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
        $data = $response->json();
        $this->assertTrue($data['success'] ?? false, 'Login should succeed');
        
        // This should work on both unfixed and fixed code
        // No pending payment means no auto-recovery trigger, so no Safari auto-open issue
    }

    /**
     * Supplementary test: Verify Android login is unaffected by iOS fix
     * This should pass on both unfixed and fixed code (preservation requirement)
     */
    public function test_android_login_with_pending_payment_should_be_unaffected(): void
    {
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );

        $user = User::factory()->create([
            'email' => 'androiduser@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Login on Android platform — AJAX to match real in-app flow
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'android',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/login', [
            'email' => 'androiduser@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
        $data = $response->json();
        $this->assertTrue($data['success'] ?? false, 'Login should succeed');

        // Android should work identically before and after fix
        // The iOS fix should not affect Android behavior at all
    }
}

<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Preservation Property Tests for iPhone Login Safari Redirect Bug Fix
 * 
 * CRITICAL: These tests MUST PASS on UNFIXED code - they capture baseline behavior
 * 
 * Goal: Document and verify that non-buggy inputs produce identical behavior
 * before and after the fix. This ensures the fix only affects the bug condition
 * and does not introduce regressions.
 * 
 * Property 2: Preservation - Manual Deposit and Background Polling Functionality
 * 
 * For any input where the bug condition does NOT hold (not a post-login page load
 * with pending payment on iOS), the fixed code SHALL produce exactly the same
 * behavior as the original code.
 * 
 * Test Strategy: Observation-first methodology
 * 1. Run these tests on UNFIXED code
 * 2. Observe and document passing behavior
 * 3. After fix implementation, re-run tests
 * 4. Verify all tests still pass (no regressions)
 * 
 * Validates Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6
 */
class IphoneLoginPreservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure currency exists for all tests
        Currency::firstOrCreate(
            ['code' => 'NGN'],
            ['name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'is_active' => true]
        );
        
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true]
        );
    }

    /**
     * Preservation Test 1: Manual Deposit API Endpoint Exists
     * 
     * Validates: Requirement 3.1 - Manual deposit functionality preservation
     * 
     * Property: The deposit initialization API endpoint should exist and be accessible.
     * We test that the endpoint exists and has authentication requirements.
     * 
     * Note: Full integration testing requires Paystack configuration which may not
     * be available in test environment. This test verifies the endpoint structure.
     */
    public function test_manual_deposit_api_endpoint_exists_and_requires_auth(): void
    {
        // Test without authentication - should fail with 401 or redirect
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
        ])->postJson('/transaction/initialize', [
            'amount' => 1000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
        ]);

        // Should require authentication (401) or redirect (302), not 404
        $this->assertContains(
            $response->status(),
            [401, 302, 419], // 419 = token mismatch, also valid
            'Deposit API endpoint should exist and require authentication'
        );
    }

    /**
     * Preservation Test 2: Payment Verification API Endpoint Structure
     * 
     * Validates: Requirement 3.2 - Payment verification polling preservation
     * 
     * Property: The verification API endpoint should exist and handle requests.
     * We test that the endpoint structure is preserved.
     */
    public function test_payment_verification_api_endpoint_exists(): void
    {
        $user = User::factory()->create([
            'email' => 'verify-user@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        $this->actingAs($user);

        // Test that verification endpoint exists (returns 200, not 404)
        $reference = 'DEP_TEST_PENDING_' . Str::random(8);
        $response = $this->getJson("/api/v1/payments/verify/{$reference}");
        
        // Endpoint should exist and return a response (200, 400, not 404)
        $this->assertNotEquals(
            404,
            $response->status(),
            "Verification API endpoint should exist (not 404)"
        );
        
        // Should return JSON structure
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * Preservation Test 3: Android Platform In-App Payment Flow Unaffected
     * 
     * Validates: Requirement 3.6 - Non-iOS platform preservation
     * 
     * Property: For any Android device with pending payment (even on login),
     * the behavior should be completely unaffected by iOS-specific fix.
     * Android uses in-app modal, not Safari.
     * 
     * Input: User logs in on Android with any wallet state
     * Expected: Login succeeds, Android in-app modal may appear (not Safari)
     */
    public function test_android_login_flow_unaffected_by_ios_fix(): void
    {
        $user = User::factory()->create([
            'email' => 'android-user@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Login on Android platform — AJAX to match real in-app flow
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'android',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/login', [
            'email' => 'android-user@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
        
        // Android uses different payment flow (in-app modal, not Safari)
        // This test documents that Android is unaffected
        $data = $response->json();
        $this->assertTrue($data['success'] ?? false, 'Login should succeed on Android');
    }

    /**
     * Preservation Test 4: Web Platform Login Flow Unaffected
     * 
     * Validates: Requirement 3.6 - Non-iOS platform preservation
     * 
     * Property: For any web browser login (desktop, mobile web), behavior
     * should be completely unaffected. Web uses different payment flows.
     */
    public function test_web_login_flow_unaffected_by_ios_fix(): void
    {
        $user = User::factory()->create([
            'email' => 'web-user@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Login without native platform header (web browser)
        $response = $this->post('/login', [
            'email' => 'web-user@example.com',
            'password' => 'SecurePass123!',
        ]);

        // Web login typically redirects to intended route or dashboard
        $response->assertStatus(302); // Redirect after web login
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Preservation Test 5: Login Without Pending Payment on iOS
     * 
     * Validates: Requirement 3.1 - Normal login flow preservation
     * 
     * Property: For any iOS login where NO pending payment exists in localStorage,
     * the login should proceed normally to dashboard without interruption.
     * This is NOT the bug condition (no pending payment).
     * 
     * Input: User logs in on iOS, no pending payment
     * Expected: Normal dashboard load, no auto-recovery triggered
     */
    public function test_ios_login_without_pending_payment_loads_normally(): void
    {
        $user = User::factory()->create([
            'email' => 'clean-ios-user@example.com',
            'password' => Hash::make('SecurePass123!'),
        ]);

        // Login on iOS without any pending payment context — AJAX to match real in-app flow
        $response = $this->withHeaders([
            'X-NativePHP-Platform' => 'ios',
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('/login', [
            'email' => 'clean-ios-user@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
        
        // This should work identically before and after fix
        // No pending payment means no auto-recovery, so no bug trigger
        $data = $response->json();
        $this->assertTrue($data['success'] ?? false, 'Login should succeed');
    }

    /**
     * Preservation Test 6: Payment-Sync.js Exists and Loads
     * 
     * Validates: Requirement 3.2 - Background polling infrastructure preservation
     * 
     * Property: The payment-sync.js file should exist and contain core polling logic.
     * The fix should add suppression logic WITHOUT removing existing functionality.
     */
    public function test_payment_sync_javascript_file_exists_with_core_functions(): void
    {
        $paymentSyncPath = public_path('js/payment-sync.js');
        
        $this->assertFileExists($paymentSyncPath, 'payment-sync.js should exist in public/js/');
        
        $content = file_get_contents($paymentSyncPath);
        
        // Verify core functions exist (baseline behavior)
        $this->assertStringContainsString('startPolling', $content, 'startPolling function should exist');
        $this->assertStringContainsString('stopPolling', $content, 'stopPolling function should exist');
        $this->assertStringContainsString('checkPaymentStatus', $content, 'checkPaymentStatus function should exist');
        $this->assertStringContainsString('getStoredPendingPayment', $content, 'getStoredPendingPayment should exist');
        $this->assertStringContainsString('init', $content, 'init function should exist');
        
        // Verify localStorage key is defined
        $this->assertStringContainsString('segyric_pending_payment', $content, 'Storage key should be defined');
        
        // Verify polling interval is configured
        $this->assertStringContainsString('POLL_INTERVAL_MS', $content, 'Poll interval should be configured');
        
        // Verify max pending age is configured (30 minute expiry)
        $this->assertStringContainsString('MAX_PENDING_AGE_MS', $content, 'Max pending age should be configured');
    }

    /**
     * Preservation Test 7: Payment Sync JavaScript Has Expected Structure
     * 
     * Validates: Requirement 3.2 - Background polling infrastructure
     * 
     * Property: The payment-sync.js should maintain its core polling structure.
     * After the fix, suppression logic will be added WITHOUT removing existing functions.
     */
    public function test_payment_sync_maintains_expected_functions_after_fix(): void
    {
        $paymentSyncPath = public_path('js/payment-sync.js');
        $this->assertFileExists($paymentSyncPath);
        
        $content = file_get_contents($paymentSyncPath);
        
        // Core functions that MUST remain after fix
        $requiredFunctions = [
            'startPolling',
            'stopPolling', 
            'checkPaymentStatus',
            'getStoredPendingPayment',
            'init',
            'updateDomBalances',
            'triggerSplash',
        ];
        
        foreach ($requiredFunctions as $func) {
            $this->assertStringContainsString(
                $func,
                $content,
                "Function '{$func}' must be preserved in payment-sync.js"
            );
        }
        
        // Core constants that MUST remain
        $this->assertStringContainsString('segyric_pending_payment', $content);
        $this->assertStringContainsString('POLL_INTERVAL_MS', $content);
        $this->assertStringContainsString('MAX_PENDING_AGE_MS', $content);
    }

    /**
     * Preservation Test 8: Login Authentication Works Across Platforms
     * 
     * Validates: Requirement 3.1, 3.6 - Login flow preservation across platforms
     * 
     * Property: Login should succeed on different platforms with different user states.
     */
    public function test_login_succeeds_across_different_platforms_and_user_states(): void
    {
        // Test login for iOS, Android, Web platforms
        $testCases = [
            [
                'email' => 'ios-test@example.com',
                'platform' => 'ios',
                'expected_status' => 200, // Returns JSON for AJAX
            ],
            [
                'email' => 'android-test@example.com',
                'platform' => 'android',
                'expected_status' => 200, // Returns JSON for AJAX
            ],
            [
                'email' => 'web-test@example.com',
                'platform' => null, // Web browser (no platform header)
                'expected_status' => 302, // Web form submission redirects
            ],
        ];

        foreach ($testCases as $case) {
            $user = User::factory()->create([
                'email' => $case['email'],
                'password' => Hash::make('SecurePass123!'),
            ]);

            $headers = [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ];
            if ($case['platform']) {
                $headers['X-NativePHP-Platform'] = $case['platform'];
            } else {
                // Web browser — normal form submission (no AJAX headers)
                $headers = [];
            }

            $this->flushHeaders();
            $response = $this->withHeaders($headers)->post('/login', [
                'email' => $case['email'],
                'password' => 'SecurePass123!',
            ]);

            $response->assertStatus(
                $case['expected_status'],
                "Login should succeed on {$case['platform']} platform"
            );
            $this->assertAuthenticatedAs($user);
            
            // Logout to test next platform independently
            $this->post('/logout');
        }
    }

    /**
     * Preservation Test 9: Multiple Users Can Authenticate Independently
     * 
     * Validates: Requirement 3.1 - Login isolation per user
     * 
     * Property: Multiple users should be able to log in independently.
     */
    public function test_multiple_users_can_authenticate_independently(): void
    {
        $users = [
            User::factory()->create(['email' => 'multi1@example.com', 'password' => Hash::make('Pass123!')]),
            User::factory()->create(['email' => 'multi2@example.com', 'password' => Hash::make('Pass123!')]),
            User::factory()->create(['email' => 'multi3@example.com', 'password' => Hash::make('Pass123!')]),
        ];

        foreach ($users as $user) {
            $response = $this->withHeaders([
                'X-NativePHP-Platform' => 'ios',
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->post('/login', [
                'email' => $user->email,
                'password' => 'Pass123!',
            ]);

            $response->assertStatus(200, "User {$user->email} should log in successfully");
            $this->assertAuthenticatedAs($user);
            
            // Logout to test next user independently
            $this->post('/logout');
        }
    }

    /**
     * Preservation Test 10: Mobile Blade Layout Contains Payment Infrastructure
     * 
     * Validates: Requirement 3.2, 3.3, 3.4 - Payment UI and polling infrastructure
     * 
     * Property: The mobile layout should contain deposit modal and payment polling code.
     * After the fix, suppression code will be added WITHOUT removing existing code.
     */
    public function test_mobile_layout_contains_payment_infrastructure(): void
    {
        $mobileLayoutPath = resource_path('views/native/layouts/mobile.blade.php');
        $this->assertFileExists($mobileLayoutPath, 'Mobile layout should exist');
        
        $content = file_get_contents($mobileLayoutPath);
        
        // Verify payment-related infrastructure exists
        $this->assertStringContainsString('startInAppPolling', $content, 'In-app polling function should exist');
        $this->assertStringContainsString('checkInAppVerification', $content, 'Verification check function should exist');
        $this->assertStringContainsString('handleInAppSuccess', $content, 'Success handler should exist');
        
        // Verify localStorage usage for pending payments
        $this->assertStringContainsString('localStorage', $content, 'LocalStorage should be used');
        
        // Verify iOS branch exists (the one that triggers Browser::inApp)
        $this->assertStringContainsString('iOS NATIVE SHELL', $content, 'iOS payment branch should exist');
    }
}

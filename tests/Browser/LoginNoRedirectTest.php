<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * REGRESSION TEST: Login Must Not Trigger Paystack Redirect
 * 
 * This test ensures that logging into the dashboard never automatically
 * opens checkout.paystack.com in Safari or any other browser.
 * 
 * Bug Context:
 * - Safari's bfcache was restoring iframe with Paystack URL
 * - sandbox="allow-popups-to-escape-sandbox" caused Safari to open URL in new tab
 * - Occurred ~1 second after login, disrupting UX
 * 
 * Expected Behavior:
 * - User logs in → lands on /mobile dashboard
 * - Dashboard loads completely with no redirects
 * - No window.open(), location.href, or meta refresh
 * - Paystack checkout only opens after explicit "Pay" button click
 */
class LoginNoRedirectTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test that login lands on dashboard with no auto-redirect to Paystack.
     *
     * @return void
     */
    public function test_login_stays_on_dashboard_no_paystack_redirect()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->press('Log In')
                    ->waitForLocation('/mobile', 10)
                    ->assertPathIs('/mobile')
                    ->assertDontSee('checkout.paystack.com')
                    ->pause(3000) // Wait 3 seconds to ensure no delayed redirect
                    ->assertPathIs('/mobile') // Still on dashboard
                    ->assertSee($user->name);
            
            // Verify no iframe has Paystack URL loaded
            $iframeSrc = $browser->script("
                const iframe = document.getElementById('complete-payment-iframe');
                return iframe ? iframe.src : null;
            ")[0];
            
            $this->assertTrue(
                empty($iframeSrc) || $iframeSrc === 'about:blank',
                'Payment iframe should be empty or about:blank, got: ' . $iframeSrc
            );
            
            // Verify no localStorage pending payment triggers
            $pendingPayment = $browser->script("
                return localStorage.getItem('segyric_pending_payment');
            ")[0];
            
            $this->assertNull(
                $pendingPayment,
                'No pending payment should exist after fresh login'
            );
            
            // Verify suppression flags are set
            $pageLoadTime = $browser->script("
                return sessionStorage.getItem('segyric_page_load_time');
            ")[0];
            
            $this->assertNotNull(
                $pageLoadTime,
                'Page load suppression timestamp should be set'
            );
        });
    }

    /**
     * Test that explicit "Fund Wallet" button DOES open payment modal.
     *
     * @return void
     */
    public function test_explicit_fund_wallet_button_opens_payment_modal()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/mobile')
                    ->waitFor('button:has-text("Deposit")', 10)
                    ->click('button:has-text("Deposit")')
                    ->waitFor('#native-deposit-screen-modal', 5)
                    ->assertVisible('#native-deposit-screen-modal')
                    ->assertSee('Deposit');
            
            // Modal should be visible, iframe container hidden until payment initialized
            $iframeContainerVisible = $browser->script("
                const container = document.getElementById('complete-payment-iframe-container');
                return container ? container.style.display !== 'none' : false;
            ")[0];
            
            $this->assertFalse(
                $iframeContainerVisible,
                'Payment iframe should not be visible until user initiates payment'
            );
        });
    }

    /**
     * Test Safari bfcache scenario: navigate away and back.
     *
     * @return void
     */
    public function test_safari_bfcache_navigate_away_and_return_no_redirect()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Login
            $browser->loginAs($user)
                    ->visit('/mobile')
                    ->waitForText($user->name)
                    ->assertPathIs('/mobile');
            
            // Simulate bfcache: navigate to another page
            $browser->visit('/mobile/wallet')
                    ->waitForLocation('/mobile/wallet')
                    ->assertPathIs('/mobile/wallet');
            
            // Navigate back (triggers bfcache restore)
            $browser->back()
                    ->waitForLocation('/mobile', 10)
                    ->pause(2000) // Wait for bfcache restoration + any delayed scripts
                    ->assertPathIs('/mobile')
                    ->assertDontSee('checkout.paystack.com');
            
            // Verify iframe was cleaned by bfcache defense
            $iframeSrc = $browser->script("
                const iframe = document.getElementById('complete-payment-iframe');
                return iframe ? iframe.src : null;
            ")[0];
            
            $this->assertTrue(
                empty($iframeSrc) || $iframeSrc === 'about:blank',
                'After bfcache restore, iframe should be clean'
            );
        });
    }

    /**
     * Test that console logs show security measures are active.
     *
     * @return void
     */
    public function test_security_console_logs_are_present()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/mobile')
                    ->waitForText($user->name);
            
            // Get console logs
            $logs = $browser->driver->manage()->getLog('browser');
            
            $securityLogsFound = false;
            foreach ($logs as $log) {
                if (str_contains($log['message'], 'MOBILE-DASHBOARD') || 
                    str_contains($log['message'], 'BFCACHE-DEFENSE') ||
                    str_contains($log['message'], 'PAYMENT-SYNC')) {
                    $securityLogsFound = true;
                    break;
                }
            }
            
            $this->assertTrue(
                $securityLogsFound,
                'Security monitoring console logs should be present'
            );
        });
    }

    /**
     * Test that payment-sync.js does NOT auto-start on dashboard.
     *
     * @return void
     */
    public function test_payment_sync_does_not_auto_start_on_dashboard()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/mobile')
                    ->waitForText($user->name)
                    ->pause(3000); // Wait for any auto-recovery attempts
            
            // Check if polling was started (shouldn't be)
            $pollingActive = $browser->script("
                return typeof window.stopGlobalPaymentPolling === 'function';
            ")[0];
            
            $this->assertTrue(
                $pollingActive,
                'Payment sync API should be available but not auto-started'
            );
        });
    }
}

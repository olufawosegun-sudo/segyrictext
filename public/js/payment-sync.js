/**
 * Verixa Global Real-Time Payment Poller & Synchronizer
 * Enables asynchronous background payment verification, multi-tasking during checkout,
 * and instant zero-latency balance reflection upon payment completion.
 */
(function() {
    'use strict';

    const STORAGE_KEY = 'verixa_pending_payment';
    const LEGACY_STORAGE_KEY = 'segyric_pending_payment';
    const POLL_INTERVAL_MS = 2500;
    const MAX_PENDING_AGE_MS = 30 * 60 * 1000; // 30 minutes

    let pollTimer = null;
    let isChecking = false;

    /**
     * Update all balance DOM elements on screen
     */
    function updateDomBalances(newBalance, currencySymbol) {
        if (newBalance === undefined || newBalance === null) return;
        const formatted = Number(newBalance).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // 1. Desktop sidebar balance
        const desktopSidebar = document.getElementById('desktop-sidebar-balance');
        if (desktopSidebar) {
            desktopSidebar.textContent = (currencySymbol || '') + formatted;
        }

        // 2. Desktop top navigation balance
        const desktopNav = document.getElementById('desktop-nav-balance');
        if (desktopNav) {
            desktopNav.textContent = (currencySymbol || '') + formatted;
        }

        // 3. Native mobile wallet screen balance
        const nativeWallet = document.getElementById('native-wallet-balance-num');
        if (nativeWallet) {
            nativeWallet.textContent = formatted;
        }

        // 4. Native mobile header balance
        const nativeHeader = document.getElementById('native-header-balance-num');
        if (nativeHeader) {
            nativeHeader.textContent = formatted;
        }

        // 5. Generic balance attributes
        document.querySelectorAll('[data-wallet-balance]').forEach(el => {
            el.textContent = (currencySymbol || '') + formatted;
        });

        // 6. If Alpine.js refill component is active, update reactive state
        if (window.Alpine) {
            document.querySelectorAll('[x-data]').forEach(el => {
                if (el._x_dataStack && el._x_dataStack[0] && el._x_dataStack[0].currentBalance !== undefined) {
                    el._x_dataStack[0].currentBalance = parseFloat(newBalance);
                }
            });
        }
    }

    /**
     * Trigger celebratory splash overlay
     */
    function triggerSplash(symbol, amount) {
        if (typeof window.showDesktopPaymentSplash === 'function') {
            window.showDesktopPaymentSplash(symbol, amount);
        } else if (typeof window.showPaymentSplash === 'function') {
            window.showPaymentSplash(symbol, amount);
        } else {
            const overlay = document.getElementById('payment-splash-overlay') || document.getElementById('desktop-payment-splash');
            if (overlay) {
                overlay.style.display = 'flex';
                setTimeout(() => { overlay.style.display = 'none'; }, 4500);
            }
        }
    }

    /**
     * Poll backend for payment verification
     */
    async function checkPaymentStatus(pending) {
        if (isChecking || !pending || !pending.reference) return;
        isChecking = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const basePath = window.location.pathname.includes('/segyrictext/public') ? '/segyrictext/public' : '';
            const verifyUrl = basePath + '/api/v1/payments/verify/' + encodeURIComponent(pending.reference);
            
            const res = await fetch(verifyUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!res.ok && res.status !== 200) {
                // If 404 or terminal error, don't throw, let next tick handle or wait
                isChecking = false;
                return;
            }

            const data = await res.json();

            // Check if paid or already processed
            if (data.success && (data.status === 'paid' || data.status === 'already_paid')) {
                // Stop polling & clear localStorage
                stopPolling();
                localStorage.removeItem(STORAGE_KEY);
                localStorage.removeItem(LEGACY_STORAGE_KEY);

                const newBalance = data.wallet ? data.wallet.available_balance : null;
                const creditAmount = data.amount || pending.amount;
                const symbol = pending.symbol || (data.wallet?.currency?.symbol ?? '₦');

                // Update UI balances instantly
                if (newBalance !== null) {
                    updateDomBalances(newBalance, symbol);
                }

                // Show instant celebration splash
                triggerSplash(symbol, creditAmount);

                // Dispatch global event for any active page listeners
                const eventPayload = {
                    detail: {
                        reference: pending.reference,
                        amount: creditAmount,
                        wallet: data.wallet,
                        payment: data.payment
                    }
                };
                window.dispatchEvent(new CustomEvent('verixa:payment-confirmed', eventPayload));
                window.dispatchEvent(new CustomEvent('segyric:payment-confirmed', eventPayload));

                // Auto reload if on transaction ledger after a short delay
                const currentPath = window.location.pathname;
                if (currentPath.includes('/wallet') || currentPath.includes('/dashboard')) {
                    setTimeout(() => {
                        if (typeof window.navigateSpa === 'function') {
                            window.navigateSpa(window.location.href, false);
                        }
                    }, 2500);
                }
            }
        } catch (err) {
            console.debug('Background payment poll status check:', err);
        } finally {
            isChecking = false;
        }
    }

    /**
     * Start background polling timer
     */
    function startPolling(pending) {
        stopPolling();
        if (!pending || !pending.reference) return;

        // Run an immediate check right away
        checkPaymentStatus(pending);

        // Schedule periodic polls
        pollTimer = setInterval(() => {
            const current = getStoredPendingPayment();
            if (!current) {
                stopPolling();
                return;
            }
            // Check if pending has expired
            if (Date.now() - current.startedAt > MAX_PENDING_AGE_MS) {
                stopPolling();
                localStorage.removeItem(STORAGE_KEY);
                return;
            }
            checkPaymentStatus(current);
        }, POLL_INTERVAL_MS);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
        isChecking = false;
    }

    function getStoredPendingPayment() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            const data = JSON.parse(raw);
            if (!data.reference || (Date.now() - (data.startedAt || 0) > MAX_PENDING_AGE_MS)) {
                localStorage.removeItem(STORAGE_KEY);
                return null;
            }
            return data;
        } catch (e) {
            localStorage.removeItem(STORAGE_KEY);
            return null;
        }
    }

    /**
     * Public API on window
     */
    window.startGlobalPaymentPolling = function(opts) {
        if (!opts || !opts.reference) return;
        const pending = {
            reference: opts.reference,
            amount: parseFloat(opts.amount) || 0,
            symbol: opts.symbol || '$',
            currency: opts.currency || 'USD',
            startedAt: Date.now()
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(pending));
        startPolling(pending);
    };

    window.stopGlobalPaymentPolling = function() {
        stopPolling();
        localStorage.removeItem(STORAGE_KEY);
    };

    /**
     * Check if auto-recovery should be suppressed (e.g. right after login).
     * Prevents checkout.paystack.com from auto-opening in Safari on iOS after login.
     */
    function shouldSuppressAutoRecovery() {
        try {
            // Check 1: Recent login flag (30 seconds window)
            const loginTs = sessionStorage.getItem('verixa_just_logged_in') 
                || sessionStorage.getItem('segyric_just_logged_in')
                || sessionStorage.getItem('Verixa_just_logged_in');
            if (loginTs) {
                const elapsed = Date.now() - parseInt(loginTs, 10);
                // Suppress for 30 seconds after login
                if (elapsed >= 0 && elapsed < 30000) {
                    return true;
                }
                // Expired — clean up
                sessionStorage.removeItem('verixa_just_logged_in');
                sessionStorage.removeItem('segyric_just_logged_in');
                sessionStorage.removeItem('Verixa_just_logged_in');
            }

            // Check 2: Detect if we're on the dashboard/home page JUST after page load
            // Suppress auto-recovery for 5 seconds after any page load to prevent jarring UX
            const pageLoadTs = sessionStorage.getItem('verixa_page_load_time') 
                || sessionStorage.getItem('segyric_page_load_time')
                || sessionStorage.getItem('Verixa_page_load_time');
            if (!pageLoadTs) {
                // First load - set timestamp and suppress
                const now = String(Date.now());
                sessionStorage.setItem('verixa_page_load_time', now);
                sessionStorage.setItem('segyric_page_load_time', now);
                sessionStorage.setItem('Verixa_page_load_time', now);
                return true;
            } else {
                const pageLoadElapsed = Date.now() - parseInt(pageLoadTs, 10);
                if (pageLoadElapsed < 5000) {
                    return true; // Suppress for 5 seconds after page load
                }
            }

            // Check 3: If user is on login/register/dashboard page, ALWAYS suppress
            const path = window.location.pathname;
            if (path.includes('/login') || path.includes('/register') || path.includes('/auth') || path === '/mobile' || path.endsWith('/mobile')) {
                return true;
            }
        } catch (e) {
            console.debug('[PAYMENT-SYNC] shouldSuppressAutoRecovery error:', e);
        }
        return false;
    }

    // Auto-resume polling on initial page load if payment pending
    function init() {
        const pending = getStoredPendingPayment();
        const path = window.location.pathname;
        
        console.log('[PAYMENT-SYNC] payment-sync.js initialized on page load', {
            has_pending_payment: !!pending,
            pending_reference: pending ? pending.reference : null,
            current_path: path
        });

        // CRITICAL FIX: If on mobile dashboard/home, NEVER auto-recover
        // This is the landing page after login - user should see their dashboard first
        if ((path === '/mobile' || path.endsWith('/mobile') || path.includes('/mobile')) && !path.includes('/wallet')) {
            if (pending) {
                console.log('[PAYMENT-SYNC] On mobile dashboard - payment recovery disabled. User must manually click "Continue Payment" button.');
            }
            return; // Exit immediately - no auto-recovery on dashboard
        }

        // CRITICAL FIX: Always suppress auto-recovery on page load
        // User must manually click "Continue Payment" or "Fund Wallet" button
        // This prevents unwanted auto-opening of checkout.paystack.com on login
        if (pending && shouldSuppressAutoRecovery()) {
            console.log('[PAYMENT-SYNC] Auto-recovery suppressed. User must manually resume payment.');
            // DO NOT clear localStorage - keep payment for manual recovery
            // Just don't auto-start polling
            return;
        }

        // Only start polling if user is ACTIVELY on a payment/wallet page
        // Never auto-start on dashboard/home landing
        const isPaymentContext = path.includes('/wallet') || 
                                 path.includes('/deposit') || 
                                 path.includes('/payment') ||
                                 path.includes('/checkout');
        
        if (pending && !isPaymentContext) {
            console.log('[PAYMENT-SYNC] Pending payment found, but not in payment context. Suppressing auto-recovery.');
            console.log('[PAYMENT-SYNC] User can resume payment by visiting wallet/deposit page.');
            return;
        }

        if (pending && isPaymentContext) {
            console.log('[PAYMENT-SYNC] Pending payment found in payment context. Starting polling.');
            startPolling(pending);
        }
    }

    // On visibility change / focus (e.g. user returns from banking app or another tab)
    function onResume() {
        const path = window.location.pathname;
        
        // CRITICAL FIX: Never auto-resume on mobile dashboard
        if ((path === '/mobile' || path.endsWith('/mobile') || path.includes('/mobile')) && !path.includes('/wallet')) {
            console.log('[PAYMENT-SYNC] onResume suppressed - on mobile dashboard');
            return;
        }

        // CRITICAL FIX: Also suppress during auto-recovery suppression windows
        if (shouldSuppressAutoRecovery()) {
            console.log('[PAYMENT-SYNC] onResume suppressed during suppression window');
            return;
        }

        // Only resume if in payment context
        const isPaymentContext = path.includes('/wallet') || 
                                 path.includes('/deposit') || 
                                 path.includes('/payment') ||
                                 path.includes('/checkout');
        
        if (!isPaymentContext) {
            console.log('[PAYMENT-SYNC] onResume suppressed - not in payment context');
            return;
        }

        const pending = getStoredPendingPayment();
        if (pending) {
            checkPaymentStatus(pending);
            if (!pollTimer) {
                startPolling(pending);
            }
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) onResume();
    });

    window.addEventListener('focus', onResume);

    // SECURITY FIX: Remove pageshow listener that was causing premature payment checks
    // The init() function already handles initial load properly with suppression logic
    // window.addEventListener('pageshow', onResume); // REMOVED - was triggering on bfcache restore

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

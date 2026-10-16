@extends('native.layouts.mobile')

@section('title', 'Deposit')

@section('head_styles')
<style>
    /* ═══════════════════════════════════════════════════════════════
       DEPOSIT PAGE — 100% STATIC, LOCKED VIEWPORT (ZERO BOUNCE / ZERO SLIDE)
    ═══════════════════════════════════════════════════════════════ */
    html, body {
        width: 100% !important;
        height: 100% !important;
        min-height: 100% !important;
        max-height: 100% !important;
        background: #070B14 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
        overscroll-behavior: none !important;
        overscroll-behavior-y: none !important;
        touch-action: pan-x pan-y !important;
        position: fixed !important;
        inset: 0 !important;
    }

    /* Hide bottom tab bar on deposit page */
    .tab-bar,
    #blitz-tab-bar {
        display: none !important;
    }

    /* Lock scroll-content firmly stationary — no rubber-banding or slack */
    .scroll-content,
    #blitz-scroll-content {
        width: 100% !important;
        max-width: 100% !important;
        height: calc(100% - 56px) !important;
        max-height: calc(100% - 56px) !important;
        flex: 1 !important;
        padding: 0 !important;
        padding-bottom: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
        overflow-y: hidden !important;
        overscroll-behavior: none !important;
        overscroll-behavior-y: none !important;
        -webkit-overflow-scrolling: auto !important;
        background: #070B14 !important;
        touch-action: pan-x pan-y !important;
    }

    /* Expand app-shell to 100% width and height on ALL screens */
    .app-shell {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        height: 100vh !important;
        height: 100dvh !important;
        min-height: 100vh !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #070B14 !important;
        overflow: hidden !important;
        overscroll-behavior: none !important;
    }

    @media (min-width: 480px) {
        body {
            background: #070B14 !important;
            display: block !important;
            align-items: stretch !important;
            justify-content: stretch !important;
        }
        .app-shell {
            max-width: 100% !important;
            width: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            border-radius: 0 !important;
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
    }

    /* Completely hide & neutralize pull-to-refresh */
    .blitz-ptr,
    #blitz-ptr {
        display: none !important;
        height: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /* Static status bar */
    .status-bar {
        background: #070B14 !important;
        width: 100% !important;
    }
</style>
@endsection

@section('custom_top_bar')
<header class="deposit-top-bar" style="position: relative; display: flex; align-items: center; justify-content: space-between; height: 56px; padding: 0 16px; background: rgba(7, 11, 20, 0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid rgba(255, 255, 255, 0.06); z-index: 20;">
    <a href="{{ route('mobile.wallet') }}" onclick="if(window.history.length > 1) { window.history.back(); return false; }" class="deposit-back-btn" aria-label="Go Back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </a>
    
    <span class="deposit-title-text">Fund Wallet</span>

    <div style="width: 40px; height: 40px;"></div>
</header>
@endsection

@section('content')
<div class="deposit-fullscreen">

    <!-- ═══ 1. Balance Hero Section ═══ -->
    <div class="deposit-balance-hero">
        <div class="deposit-balance-glow"></div>
        <div class="deposit-balance-glow-2"></div>
        <span class="deposit-balance-label">Available Balance</span>
        <div class="deposit-balance-amount" id="deposit-hero-balance">
            {{ $wallet->currency->symbol ?? '₦' }}{{ number_format($wallet->available_balance ?? 0, 2) }}
        </div>
        <div class="deposit-balance-badge">
            <span class="deposit-badge-dot"></span>
            <span>Wallet Active</span>
        </div>
    </div>

    <!-- ═══ 2. Amount Input Card (Glassmorphic) ═══ -->
    <div class="deposit-amount-card">
        <label class="deposit-card-label" for="deposit-amount-input">Enter amount ({{ $wallet->currency->symbol ?? '₦' }})</label>
        
        <!-- Large Centered Amount Display -->
        <div class="deposit-amount-display" id="deposit-amount-display">
            <span class="deposit-amount-currency">{{ $wallet->currency->symbol ?? '₦' }}</span>
            <span class="deposit-amount-value" id="deposit-amount-display-value">{{ number_format((int) $initialAmount) }}</span>
        </div>

        <!-- Hidden Actual Input (for keyboard entry) -->
        <div class="deposit-input-wrap">
            <span class="deposit-input-prefix">{{ $wallet->currency->symbol ?? '₦' }}</span>
            <input 
                type="number" 
                id="deposit-amount-input" 
                value="{{ (int) $initialAmount }}" 
                min="100" 
                step="100" 
                placeholder="Enter amount"
                class="deposit-input-field"
                inputmode="numeric"
                oninput="syncDepositDisplay()"
                onfocus="this.parentElement.classList.add('focused')"
                onblur="this.parentElement.classList.remove('focused')"
            />
        </div>

        <!-- Quick Presets -->
        <div class="deposit-presets-grid">
            <button type="button" class="deposit-preset-pill blitz-btn" onclick="setPresetAmount(500, this)" data-amount="500">
                {{ $wallet->currency->symbol ?? '₦' }}500
            </button>
            <button type="button" class="deposit-preset-pill blitz-btn" onclick="setPresetAmount(1000, this)" data-amount="1000">
                {{ $wallet->currency->symbol ?? '₦' }}1,000
            </button>
            <button type="button" class="deposit-preset-pill blitz-btn active-preset" onclick="setPresetAmount(2000, this)" data-amount="2000">
                {{ $wallet->currency->symbol ?? '₦' }}2,000
            </button>
            <button type="button" class="deposit-preset-pill blitz-btn" onclick="setPresetAmount(5000, this)" data-amount="5000">
                {{ $wallet->currency->symbol ?? '₦' }}5,000
            </button>
            <button type="button" class="deposit-preset-pill blitz-btn" onclick="setPresetAmount(10000, this)" data-amount="10000">
                {{ $wallet->currency->symbol ?? '₦' }}10K
            </button>
            <button type="button" class="deposit-preset-pill blitz-btn" onclick="setPresetAmount(20000, this)" data-amount="20000">
                {{ $wallet->currency->symbol ?? '₦' }}20K
            </button>
        </div>

        <div id="deposit-amount-error" class="deposit-error-msg"></div>
    </div>

    <!-- ═══ 3. Payment Method Card ═══ -->
    <div class="deposit-section">
        <label class="deposit-section-label">Payment Method</label>

        <div class="deposit-gateway-card selected" id="method-card-paystack">
            <div class="deposit-gateway-left">
                <!-- Paystack Logo Icon -->
                <div class="deposit-gateway-icon paystack-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="3" rx="1" fill="#FFFFFF"/>
                        <rect x="3" y="9" width="18" height="3" rx="1" fill="#FFFFFF"/>
                        <rect x="3" y="14" width="12" height="3" rx="1" fill="#FFFFFF"/>
                        <rect x="3" y="19" width="8" height="2" rx="1" fill="#FFFFFF" opacity="0.6"/>
                    </svg>
                </div>
                <div class="deposit-gateway-info">
                    <span class="deposit-gateway-name">Paystack</span>
                    <span class="deposit-gateway-desc">Cards • Bank Transfer • USSD • QR</span>
                </div>
            </div>
            
            <!-- Selected Radio -->
            <div class="deposit-gateway-radio selected">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- ═══ 4. Primary CTA Button ═══ -->
    <div class="deposit-cta-section">
        <button 
            type="button" 
            id="fund-wallet-btn" 
            data-testid="deposit-submit-btn"
            onclick="handleFundWalletSubmit()" 
            class="blitz-btn deposit-cta-btn"
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20M2 12h20"/>
            </svg>
            <span id="fund-wallet-btn-label">Fund Wallet — {{ $wallet->currency->symbol ?? '₦' }}{{ number_format((int) $initialAmount) }}</span>
        </button>
        <span id="deposit-submit-btn" style="display:none;"></span>

        <!-- Trust Badge -->
        <div class="deposit-trust-badge">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="#7C3AED">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
            </svg>
            <span>Secured by Paystack</span>
        </div>
    </div>

    <!-- Active Deposit Verification Indicator -->
    <div id="active-deposit-pill" class="deposit-active-pill">
        <div class="deposit-active-pill-left">
            <span class="deposit-active-dot"></span>
            <span id="active-deposit-text" class="deposit-active-text">Awaiting Payment...</span>
        </div>
        <button type="button" onclick="verifyCurrentPaymentImmediately()" class="deposit-verify-btn blitz-btn">
            Verify Now
        </button>
    </div>

</div>


<script>
    /**
     * ══════════════════════════════════════════════════════════════════════════
     * NATIVE DEPOSIT CONTROLLER — PAYSTACK
     * Uses Paystack In-App Checkout for 100% in-app payment
     * Zero Safari redirects, zero external browser handoffs
     * ══════════════════════════════════════════════════════════════════════════
     */

    const currencySymbol = "{{ $wallet->currency->symbol ?? '₦' }}";
    const currencyCode = "{{ $wallet->currency->code ?? 'NGN' }}";
    const userEmail = "{{ auth()->user()?->email }}";
    const userName = "{{ auth()->user()?->name }}";

    let _activeReference = null;
    let _activeAmount = 0;
    let _pollTimer = null;
    let _isVerifying = false;

    /**
     * Sync the large display with the input value
     */
    function syncDepositDisplay() {
        const input = document.getElementById('deposit-amount-input');
        const displayValue = document.getElementById('deposit-amount-display-value');
        const btnLabel = document.getElementById('fund-wallet-btn-label');
        const val = input ? parseFloat(input.value) : 0;

        if (displayValue) {
            displayValue.textContent = (isNaN(val) || val <= 0) ? '0' : val.toLocaleString();
        }

        if (btnLabel) {
            const formatted = currencySymbol + ((isNaN(val) || val <= 0) ? '0' : val.toLocaleString());
            btnLabel.textContent = 'Fund Wallet — ' + formatted;
        }

        // Clear active preset styling
        document.querySelectorAll('.deposit-preset-pill').forEach(p => p.classList.remove('active-preset'));
        // Re-activate matching preset if exists
        document.querySelectorAll('.deposit-preset-pill').forEach(p => {
            if (parseInt(p.dataset.amount) === val) {
                p.classList.add('active-preset');
            }
        });

        const err = document.getElementById('deposit-amount-error');
        if (err) err.style.display = 'none';
    }

    /**
     * Quick Preset Click
     */
    function setPresetAmount(val, btnEl) {
        const input = document.getElementById('deposit-amount-input');
        if (input) {
            input.value = val;
        }
        
        document.querySelectorAll('.deposit-preset-pill').forEach(p => p.classList.remove('active-preset'));
        if (btnEl) btnEl.classList.add('active-preset');

        const err = document.getElementById('deposit-amount-error');
        if (err) err.style.display = 'none';

        syncDepositDisplay();

        if (window.navigator && typeof window.navigator.vibrate === 'function') {
            try { window.navigator.vibrate(15); } catch(e) {}
        }
    }

    function syncAmountButtonLabel() {
        syncDepositDisplay();
    }

    /**
     * ═══════════════════════════════════════════════════════════════
     * MAIN: Fund Wallet → Initialize Paystack → In-App Sheet
     * ═══════════════════════════════════════════════════════════════
     */
    let _depositSubmitInFlight = false;

    function handleFundWalletSubmit() {
        if (_depositSubmitInFlight) return;

        const input = document.getElementById('deposit-amount-input');
        const err = document.getElementById('deposit-amount-error');
        const amt = parseFloat(input ? input.value : 0);

        if (isNaN(amt) || amt < 100) {
            if (err) {
                err.textContent = 'Please enter a valid amount (minimum ' + currencySymbol + '100).';
                err.style.display = 'block';
            }
            if (input) input.focus();
            return;
        }
        if (err) err.style.display = 'none';

        _depositSubmitInFlight = true;
        setTimeout(function() {
            _depositSubmitInFlight = false;
        }, 1500);

        // Sync with the in-app payment system in mobile layout
        const inAppInput = document.getElementById('native-deposit-amount-input');
        if (inAppInput) inAppInput.value = amt;
        if (typeof updateInAppAmountLabel === 'function') {
            updateInAppAmountLabel();
        }
        if (typeof startCompletePaymentFlow === 'function') {
            startCompletePaymentFlow();
        }
    }



    function setButtonLoading(isLoading) {
        const btn = document.getElementById('fund-wallet-btn');
        const label = document.getElementById('fund-wallet-btn-label');
        if (btn) {
            btn.disabled = isLoading;
            if (isLoading) {
                btn.classList.add('loading');
            } else {
                btn.classList.remove('loading');
            }
        }
        if (label) {
            if (isLoading) {
                label.innerHTML = '<span class="deposit-btn-loading"><span class="deposit-spinner"></span> Connecting to Paystack...</span>';
            } else {
                syncDepositDisplay();
            }
        }
    }

    function showActivePill(ref) {
        const pill = document.getElementById('active-deposit-pill');
        const text = document.getElementById('active-deposit-text');
        if (text) text.textContent = 'Ref: ' + ref.substring(0, 16) + '...';
        if (pill) pill.style.display = 'flex';
    }

    function hideActivePill() {
        const pill = document.getElementById('active-deposit-pill');
        if (pill) pill.style.display = 'none';
    }

    function startVerificationPoller(ref, amt) {
        stopVerificationPoller();
        _pollTimer = setInterval(function() {
            verifyPaymentWithFeedback(ref, amt, false);
        }, 3000);
    }

    function stopVerificationPoller() {
        if (_pollTimer) {
            clearInterval(_pollTimer);
            _pollTimer = null;
        }
    }

    function verifyCurrentPaymentImmediately() {
        if (_activeReference) {
            verifyPaymentWithFeedback(_activeReference, _activeAmount, true);
        } else {
            checkStoredPendingSession();
        }
    }

    async function verifyPaymentWithFeedback(ref, amt, isManual = false) {
        if (!ref || _isVerifying) return;
        _isVerifying = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch("{{ url('/wallet/verify') }}/" + encodeURIComponent(ref), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();

            if (data.status && data.verified) {
                console.log('[In-App Payment] Verified successfully!', data);
                stopVerificationPoller();
                hideActivePill();

                try {
                    localStorage.removeItem('pending_deposit_ref');
                    localStorage.removeItem('pending_deposit_amt');
                    localStorage.removeItem('pending_deposit_time');
                } catch(e) {}

                onPaymentCompleteSuccess(ref, amt || data.amount, data.wallet_balance);
            } else if (data.canceled || data.status === 'cancelled') {
                handlePaymentCancelled(ref);
            } else if (isManual) {
                const err = document.getElementById('deposit-amount-error');
                if (err) {
                    err.textContent = data.message || 'Payment is still confirming. Please wait a moment.';
                    err.style.display = 'block';
                    setTimeout(() => { err.style.display = 'none'; }, 4000);
                }
            }
        } catch(e) {
            console.debug('Verification poll exception:', e);
        } finally {
            _isVerifying = false;
        }
    }

    function handlePaymentCancelled(ref) {
        console.log('[Deposit] Payment cancelled for reference:', ref);
        stopVerificationPoller();
        hideActivePill();
        setButtonLoading(false);
        _activeReference = null;

        try {
            localStorage.removeItem('pending_deposit_ref');
            localStorage.removeItem('pending_deposit_amt');
            localStorage.removeItem('pending_deposit_time');
        } catch(e) {}

        const err = document.getElementById('deposit-amount-error');
        if (err) {
            err.textContent = 'Deposit was cancelled. No charges were made.';
            err.style.color = '#94A3B8';
            err.style.display = 'block';
            setTimeout(() => {
                err.style.display = 'none';
                err.style.color = '';
            }, 5000);
        }
    }
    window.handlePaymentCancelled = handlePaymentCancelled;

    // Listen for cancellation or success broadcast from in-app browser
    window.addEventListener('message', function(event) {
        if (!event.data) return;
        if (event.data.type === 'PAYMENT_CANCELLED') {
            handlePaymentCancelled(event.data.reference || _activeReference);
        } else if (event.data.type === 'PAYMENT_CALLBACK_SUCCESS') {
            verifyPaymentWithFeedback(event.data.reference || _activeReference, _activeAmount, true);
        }
    });

    window.addEventListener('storage', function(e) {
        if (e.key === 'last_payment_event' && e.newValue) {
            try {
                const data = JSON.parse(e.newValue);
                if (data.type === 'PAYMENT_CANCELLED') {
                    handlePaymentCancelled(data.reference || _activeReference);
                } else if (data.type === 'PAYMENT_CALLBACK_SUCCESS') {
                    verifyPaymentWithFeedback(data.reference || _activeReference, _activeAmount, true);
                }
            } catch(err) {}
        }
    });

    function onPaymentCompleteSuccess(ref, amt, newBalance) {
        if (newBalance !== undefined && newBalance !== null) {
            try {
                localStorage.setItem('user_wallet_balance', newBalance);
                window.dispatchEvent(new CustomEvent('balance-updated', { detail: { balance: newBalance } }));
                // Update the hero balance display
                const heroBalance = document.getElementById('deposit-hero-balance');
                if (heroBalance) {
                    heroBalance.textContent = currencySymbol + Number(newBalance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            } catch(e) {}
        }

        if (window.navigator && typeof window.navigator.vibrate === 'function') {
            try { window.navigator.vibrate([40, 60, 80]); } catch(e) {}
        }

        if (typeof window.showPaymentSplash === 'function') {
            window.showPaymentSplash(currencyCode, amt, function() {
                window.location.href = "{{ route('mobile.wallet') }}";
            });
        } else {
            setTimeout(function() {
                window.location.href = "{{ route('mobile.wallet') }}";
            }, 600);
        }
    }

    async function checkStoredPendingSession() {
        try {
            // Suppress auto-recovery right after login to prevent Safari/checkout auto-open
                const loginTs = sessionStorage.getItem('Verixa_just_logged_in')
                    || sessionStorage.getItem('verixa_just_logged_in')
                    || sessionStorage.getItem('segyric_just_logged_in');
                if (loginTs) {
                    const elapsed = Date.now() - parseInt(loginTs, 10);
                    if (elapsed >= 0 && elapsed < 30000) {
                        console.log('[DEPOSIT] Auto-recovery suppressed (just logged in). Clearing stale pending data.');
                        localStorage.removeItem('pending_deposit_ref');
                        localStorage.removeItem('pending_deposit_amt');
                        localStorage.removeItem('pending_deposit_time');
                        return;
                    }
                }
            } catch(ex) {}

            const pendingRef = localStorage.getItem('pending_deposit_ref');
            const pendingAmt = parseFloat(localStorage.getItem('pending_deposit_amt') || '0');
            const pendingTime = parseInt(localStorage.getItem('pending_deposit_time') || '0', 10);

            if (!pendingRef) return;

            if (Date.now() - pendingTime > 30 * 60 * 1000) {
                localStorage.removeItem('pending_deposit_ref');
                localStorage.removeItem('pending_deposit_amt');
                localStorage.removeItem('pending_deposit_time');
                return;
            }

            _activeReference = pendingRef;
            _activeAmount = pendingAmt;

            showActivePill(pendingRef);
            verifyPaymentWithFeedback(pendingRef, pendingAmt, false);
        } catch(e) {}
    }

    function checkUrlCancellation() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('canceled') === '1' || urlParams.get('canceled') === 'true') {
                console.log('[Deposit] Arrived with canceled flag, clearing pending session');
                handlePaymentCancelled(urlParams.get('reference') || _activeReference);
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
                return true;
            }
        } catch(e) {}
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncDepositDisplay();
        if (!checkUrlCancellation()) {
            setTimeout(checkStoredPendingSession, 400);
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            if (!checkUrlCancellation()) {
                checkStoredPendingSession();
            }
        }
    });

    window.addEventListener('focus', function() {
        if (!checkUrlCancellation()) {
            checkStoredPendingSession();
        }
    });

    window.addEventListener('pageshow', function() {
        if (!checkUrlCancellation()) {
            checkStoredPendingSession();
        }
    });

    // Stop all pull-to-refresh dragging and overscroll bounce on this page
    document.addEventListener('DOMContentLoaded', function() {
        const scrollEl = document.getElementById('blitz-scroll-content');
        if (scrollEl) {
            scrollEl.addEventListener('touchstart', function(e) {
                e.stopPropagation();
            }, { passive: true });
            scrollEl.addEventListener('touchmove', function(e) {
                e.stopPropagation();
            }, { passive: true });
        }
    });
</script>

<style>
    /* ═══════════════════════════════════════════════════════════════════════
       DEPOSIT PAGE — PREMIUM FULL-SCREEN REDESIGN
       Agent 2 (UI Designer): Glassmorphic dark-mode fintech aesthetic
    ═══════════════════════════════════════════════════════════════════════ */

    /* Top Bar */
    .deposit-back-btn {
        color: #FFFFFF;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: background 0.15s ease, transform 0.1s ease;
    }
    .deposit-back-btn:active {
        background: rgba(255, 255, 255, 0.14);
        transform: scale(0.92);
    }
    .deposit-title-text {
        font-size: 18px;
        font-weight: 800;
        color: #FFFFFF;
        letter-spacing: -0.02em;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Static Full Screen Container */
    .deposit-fullscreen {
        background: linear-gradient(175deg, #070B14 0%, #0A1128 40%, #0D1B3F 75%, #0F2347 100%);
        height: 100% !important;
        max-height: 100% !important;
        min-height: 0 !important;
        width: 100%;
        padding: 14px 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        box-sizing: border-box;
        color: #FFFFFF;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        overflow: hidden;
    }

    @media (min-width: 600px) {
        .deposit-fullscreen {
            max-width: 520px;
            margin: 0 auto;
        }
    }

    /* ── Balance Hero ── */
    .deposit-balance-hero {
        position: relative;
        text-align: center;
        padding: 8px 14px 4px;
        overflow: hidden;
    }
    .deposit-balance-glow {
        position: absolute;
        top: -40px;
        left: 50%;
        transform: translateX(-50%);
        width: 200px;
        height: 100px;
        background: radial-gradient(ellipse, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .deposit-balance-glow-2 {
        position: absolute;
        bottom: -20px;
        right: -30px;
        width: 120px;
        height: 80px;
        background: radial-gradient(ellipse, rgba(56, 189, 248, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .deposit-balance-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #7C3AED;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: rgba(16, 185, 129, 0.1);
        padding: 3px 12px;
        border-radius: 100px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .deposit-balance-amount {
        font-size: 30px;
        font-weight: 800;
        color: #FFFFFF;
        margin-top: 6px;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -0.03em;
        text-shadow: 0 0 40px rgba(16, 185, 129, 0.15);
    }
    .deposit-balance-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 11px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.5);
    }
    .deposit-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #7C3AED;
    }

    /* ── Amount Input Card (Glassmorphic) ── */
    .deposit-amount-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 16px 18px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }
    .deposit-amount-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.3), transparent);
    }

    .deposit-card-label {
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.55);
        letter-spacing: 0.02em;
        text-align: center;
    }

    /* Large Amount Display (Static) */
    .deposit-amount-display {
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 4px;
        padding: 4px 0;
        cursor: pointer;
    }
    .deposit-amount-currency {
        font-size: 24px;
        font-weight: 800;
        color: #7C3AED;
        font-family: 'JetBrains Mono', monospace;
    }
    .deposit-amount-value {
        font-size: 34px;
        font-weight: 800;
        color: #FFFFFF;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -0.03em;
        line-height: 1;
    }

    /* Actual input wrap */
    .deposit-input-wrap {
        position: relative;
        width: 100%;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        height: 52px;
        display: flex;
        align-items: center;
        padding: 0 16px;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .deposit-input-wrap.focused {
        border-color: #7C3AED;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
    .deposit-input-prefix {
        font-size: 16px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.4);
        margin-right: 8px;
        font-family: 'JetBrains Mono', monospace;
    }
    .deposit-input-field {
        width: 100%;
        background: transparent;
        border: none;
        outline: none;
        font-size: 17px;
        font-weight: 700;
        color: #FFFFFF;
        font-family: 'JetBrains Mono', monospace;
        -webkit-appearance: none;
        -moz-appearance: textfield;
    }
    .deposit-input-field::-webkit-outer-spin-button,
    .deposit-input-field::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Preset Pills */
    .deposit-presets-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .deposit-preset-pill {
        padding: 10px 0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        text-align: center;
        font-family: 'JetBrains Mono', monospace;
        transition: all 0.15s ease;
    }
    .deposit-preset-pill:active {
        transform: scale(0.94);
    }
    .deposit-preset-pill.active-preset {
        border-color: #7C3AED;
        background: rgba(16, 185, 129, 0.12);
        color: #7C3AED;
        box-shadow: 0 0 12px rgba(16, 185, 129, 0.1);
    }

    .deposit-error-msg {
        display: none;
        color: #EF4444;
        font-size: 12.5px;
        font-weight: 600;
        text-align: center;
    }

    /* ── Section ── */
    .deposit-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .deposit-section-label {
        font-size: 13px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.55);
        letter-spacing: 0.02em;
    }

    /* ── Gateway Card ── */
    .deposit-gateway-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        cursor: default;
        transition: all 0.2s ease;
    }
    .deposit-gateway-card.selected {
        border-color: rgba(16, 185, 129, 0.5);
        background: rgba(16, 185, 129, 0.06);
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.06);
    }
    .deposit-gateway-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .deposit-gateway-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .deposit-gateway-icon.paystack-icon {
        background: linear-gradient(135deg, #00C3F7 0%, #0097D1 100%);
        box-shadow: 0 4px 12px rgba(0, 195, 247, 0.25);
    }
    .deposit-gateway-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .deposit-gateway-name {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF;
        letter-spacing: -0.01em;
    }
    .deposit-gateway-desc {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.45);
        font-weight: 500;
    }
    .deposit-gateway-radio {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid rgba(255, 255, 255, 0.15);
        background: transparent;
        transition: all 0.2s ease;
    }
    .deposit-gateway-radio.selected {
        background: #7C3AED;
        border-color: #7C3AED;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    /* ── CTA Section ── */
    .deposit-cta-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
        margin-top: 2px;
    }
    .deposit-cta-btn {
        width: 100%;
        height: 52px;
        background: linear-gradient(135deg, #7C3AED 0%, #059669 50%, #047857 100%);
        color: #FFFFFF;
        border: none;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.25);
        font-family: 'Inter', -apple-system, sans-serif;
        position: relative;
        overflow: hidden;
        transition: transform 0.1s ease;
    }
    .deposit-cta-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }
    .deposit-cta-btn:hover::before {
        left: 100%;
    }
    .deposit-cta-btn:active {
        transform: scale(0.97) !important;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2);
    }
    .deposit-cta-btn.loading {
        opacity: 0.85;
        pointer-events: none;
    }

    .deposit-trust-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.4);
    }

    /* ── Loading States ── */
    .deposit-btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .deposit-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #FFFFFF;
        border-radius: 50%;
        animation: depositSpin 0.7s linear infinite;
        display: inline-block;
    }
    @keyframes depositSpin {
        to { transform: rotate(360deg); }
    }

    /* ── Active Deposit Pill ── */
    .deposit-active-pill {
        display: none;
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 16px;
        padding: 14px 16px;
        align-items: center;
        justify-content: space-between;
    }
    .deposit-active-pill-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .deposit-active-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #7C3AED;
        box-shadow: 0 0 8px #7C3AED;
        animation: depositPulse 2s ease infinite;
    }
    .deposit-active-text {
        font-size: 12px;
        font-weight: 700;
        color: #7C3AED;
        font-family: 'JetBrains Mono', monospace;
    }
    .deposit-verify-btn {
        background: linear-gradient(135deg, #7C3AED, #059669);
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }
</style>
@endsection

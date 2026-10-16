<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1E1B4B">
    <title><?php echo $__env->yieldContent('title', 'Verixa'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/verixa.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/verixa.png')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        (function() {
            var saved = localStorage.getItem('dark_mode');
            var VerixaTheme = localStorage.getItem('Verixa_theme');
            var isDark = saved === 'true' || VerixaTheme === 'dark';
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.remove('dark-theme');
            }
        })();

        window.closeInAppPaymentModal = function() {
            var modal = document.getElementById('in-app-payment-modal');
            if (modal) modal.style.display = 'none';
        };

        // Instant Native Payment Splash Feedback
        window.showPaymentSplash = function(currency, amount, onDone) {
            window.closeInAppPaymentModal();
            window.closeDepositModal();
            var splash = document.getElementById('payment-splash-overlay');
            var amtEl = document.getElementById('splash-amount-display');
            if (amtEl) {
                var sym = (currency === 'NGN' ? 'â‚¦' : (currency === 'USD' ? '$' : (currency + ' ')));
                amtEl.textContent = '+' + sym + Number(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            if (splash) {
                splash.style.display = 'flex';
                if (window.navigator && typeof window.navigator.vibrate === 'function') {
                    try { window.navigator.vibrate([40, 60, 80]); } catch(e) {}
                }
                setTimeout(function() {
                    splash.style.opacity = '0';
                    splash.style.transition = 'opacity 0.2s ease-out';
                    setTimeout(function() {
                        splash.style.display = 'none';
                        splash.style.opacity = '1';
                        if (typeof onDone === 'function') onDone();
                    }, 200);
                }, 1200);
            } else {
                if (typeof onDone === 'function') onDone();
            }
        };
    </script>
    <style>
        :root {
            --bg: #F4F6FB;
            --bg-card: #FFFFFF;
            --bg-card-elevated: #EBF0FA;
            --bg-input: #EBF0FA;
            --border: rgba(15, 23, 42, 0.08);
            --border-active: rgba(16, 185, 129, 0.4);
            --primary: #0F172A;
            --primary-navy: #0F172A;
            --primary-teal: #7C3AED;
            --primary-soft: rgba(16, 185, 129, 0.08);
            --primary-glow: rgba(16, 185, 129, 0.25);
            --cta-green: #7C3AED;
            --accent: #7C3AED;
            --success: #7C3AED;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-primary: #0F172A;
            --text-white: #0F172A;
            --text-secondary: #64748B;
            --text-tertiary: #94A3B8;
            --text-quaternary: #CBD5E1;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --nav-height: 60px;
            --header-bg: #1E1B4B;
        }

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           DARK THEME TOKENS & OVERRIDES (Bespoke Obsidian Cyber Telecom)
        â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
        [data-theme="dark"] {
            --bg: #070B14;
            --bg-card: #0B0F19;
            --bg-card-elevated: #151F36;
            --bg-input: #151F36;
            --border: rgba(255, 255, 255, 0.08);
            --border-active: rgba(16, 185, 129, 0.5);
            --primary: #7C3AED;
            --primary-navy: #0B0F19;
            --primary-teal: #7C3AED;
            --primary-soft: rgba(16, 185, 129, 0.2);
            --primary-glow: rgba(16, 185, 129, 0.4);
            --cta-green: #7C3AED;
            --accent: #7C3AED;
            --success: #7C3AED;
            --warning: #fbbf24;
            --danger: #f87171;
            --text-primary: #F8FAFC;
            --text-white: #FFFFFF;
            --text-secondary: #94A3B8;
            --text-tertiary: #64748B;
            --text-quaternary: #475569;
            --header-bg: #0B0F19;
        }
        [data-theme="dark"] body {
            background: #070B14;
            color: var(--text-primary);
        }
        [data-theme="dark"] .app-shell {
            background: var(--bg);
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-theme="dark"] .app-bar, [data-theme="dark"] .status-bar {
            background: var(--header-bg);
        }
        [data-theme="dark"] .tab-bar {
            background: #0B0F19;
            border-top-color: rgba(255, 255, 255, 0.08);
        }
        [data-theme="dark"] .tab-bar-item {
            color: #64748B;
        }
        [data-theme="dark"] .tab-bar-item.active {
            color: #7C3AED;
        }
        [data-theme="dark"] .tab-bar-item.active svg {
            color: #7C3AED;
        }
        [data-theme="dark"] .tab-bar-item.active .tab-active-pill {
            background: rgba(16, 185, 129, 0.15);
        }
        [data-theme="dark"] .card {
            background: var(--bg-card);
            border-color: var(--border);
        }
        [data-theme="dark"] .blitz-modal {
            background: #0B0F19 !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] div[style*="background: #FFFFFF"],
        [data-theme="dark"] div[style*="background:#FFFFFF"] {
            background: #0B0F19 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] div[style*="background: #EEF1F8"],
        [data-theme="dark"] div[style*="background:#EEF1F8"] {
            background: #151F36 !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] div[style*="color: #1E1B4B"],
        [data-theme="dark"] span[style*="color: #1E1B4B"],
        [data-theme="dark"] h3[style*="color: #1E1B4B"] {
            color: #F8FAFC !important;
        }
        [data-theme="dark"] div[style*="color: #6B7A99"],
        [data-theme="dark"] span[style*="color: #6B7A99"] {
            color: #94A3B8 !important;
        }



        @keyframes promoTicker {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            -webkit-tap-highlight-color: transparent; 
            touch-action: manipulation;
        }

        button, a, input, select {
            touch-action: manipulation;
            -webkit-user-select: none;
        }

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           BLITZ ULTRA-FAST INTERACTION ENGINE (Zero-Lag, Hardware-Accelerated)
        â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
        .blitz-btn, button, .tab-bar-item {
            touch-action: manipulation !important;
            cursor: pointer !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            -webkit-tap-highlight-color: transparent !important;
            position: relative;
        }
        .blitz-btn {
            will-change: transform, opacity !important;
            transition: transform 0.04s cubic-bezier(0.2, 0, 0, 1), opacity 0.04s ease, filter 0.04s ease !important;
        }
        .blitz-btn:active {
            transform: scale(0.93) translateY(1px) !important;
            opacity: 0.85 !important;
            filter: brightness(0.95) !important;
        }

        /* Blitz Splash Ripple Engine */
        .blitz-ripple {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30, 27, 75, 0.25) 0%, rgba(30, 27, 75, 0.06) 60%, transparent 100%);
            transform: scale(0);
            pointer-events: none;
            animation: blitzSplashAnim 0.26s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
            z-index: 999;
        }
        @keyframes blitzSplashAnim {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(3.5); opacity: 0; }
        }

        /* Blitz Spinner Keyframe (used by logout button loading state) */
        @keyframes blitzSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Ultra-fast button active states — every button reacts in < 16ms */
        button:active, .blitz-btn:active, [role="button"]:active, .tab-bar-item:active, .service-chip:active, .quick-amt-pill:active {
            transform: scale(0.95) !important;
            opacity: 0.85 !important;
            transition: transform 0.03s ease, opacity 0.03s ease !important;
        }

        /* Blitz Full-Screen Modal - GPU Composited, Zero-Reflow On Open */
        .blitz-modal {
            position: fixed !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: #FFFFFF !important;
            color: #1E1B4B !important;
            z-index: 500 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transform: translate3d(0, 14px, 0) scale(0.985) !important;
            transition: opacity 0.14s cubic-bezier(0.16, 1, 0.3, 1), transform 0.14s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.14s !important;
            will-change: transform, opacity !important;
            contain: paint layout;
        }
        .blitz-modal.blitz-open {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translate3d(0, 0, 0) scale(1) !important;
        }

        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            user-select: none;
            -webkit-user-select: none;
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        @media (min-width: 640px) {
            body {
                background: #E8EEF8;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                min-height: 100vh;
            }
        }

        [data-theme="dark"] body {
            background: #070B14;
        }

        /* ─── App Shell (Mobile-First Responsive Architecture) ─── */
        .app-shell {
            width: 100%;
            height: 100vh;
            height: 100dvh;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            background: var(--bg);
            transition: max-width 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Progressive Desktop Enhancement */
        @media (min-width: 640px) {
            .app-shell {
                max-width: 620px;
                box-shadow: 0 16px 48px rgba(30, 27, 75, 0.1);
                border-left: 1px solid rgba(30, 27, 75, 0.08);
                border-right: 1px solid rgba(30, 27, 75, 0.08);
            }

            .tab-bar {
                max-width: 620px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }

            /* Desktop Responsive Modals */
            .blitz-modal {
                top: 50% !important;
                left: 50% !important;
                bottom: auto !important;
                right: auto !important;
                width: 92% !important;
                max-width: 580px !important;
                height: 86vh !important;
                max-height: 820px !important;
                border-radius: 28px !important;
                box-shadow: 0 25px 60px rgba(15, 23, 42, 0.35) !important;
                border: 1px solid rgba(255, 255, 255, 0.15) !important;
                transform: translate3d(-50%, calc(-50% + 16px), 0) scale(0.985) !important;
            }
            .blitz-modal.blitz-open {
                transform: translate3d(-50%, -50%, 0) scale(1) !important;
                box-shadow: 0 0 0 100vmax rgba(15, 23, 42, 0.5), 0 25px 60px rgba(15, 23, 42, 0.35) !important;
            }
        }

        @media (min-width: 1024px) {
            .app-shell {
                max-width: 700px;
            }

            .tab-bar {
                max-width: 700px !important;
            }

            .blitz-modal {
                max-width: 640px !important;
            }
        }

        /* â€”â€”â€” Status Bar (iOS/Android-style) â€”â€”â€” */
        .status-bar {
            height: calc(var(--safe-top) + 8px);
            min-height: 12px;
            background: var(--header-bg);
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* â€”â€”â€” Top App Bar â€”â€”â€” */
        .app-bar {
            flex-shrink: 0;
            padding: 10px 20px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--header-bg);
            color: #FFFFFF;
            position: sticky;
            top: calc(var(--safe-top) + 8px);
            z-index: 1000;
        }

        .app-bar-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #FFFFFF;
        }

        .app-bar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .app-bar-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .app-bar-icon-btn:active {
            background: rgba(255, 255, 255, 0.22);
            transform: scale(0.94);
        }

        /* â€”â€”â€” Scrollable Content â€”â€”â€” */
        .scroll-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            padding: 14px 18px calc(var(--nav-height) + var(--safe-bottom) + 16px);
            scroll-behavior: smooth;
        }

        .scroll-content::-webkit-scrollbar { display: none; }

        /* â€”â€”â€” Bottom Tab Bar (PVADeals Dark Navy Style) â€”â€”â€” */
        .tab-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: calc(var(--nav-height) + var(--safe-bottom));
            padding-bottom: var(--safe-bottom);
            background: #1B2A4A;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 10005 !important;
            contain: layout style;
            border-radius: 20px 20px 0 0;
        }

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           NATIVE PULL-TO-REFRESH COMPONENT STYLES (iOS/Android Smooth Spring)
        â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
        .blitz-ptr {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translate3d(0, -6px, 0);
            transition: height 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease, transform 0.28s ease;
            will-change: height, transform, opacity;
            pointer-events: none;
            margin: 0 auto;
            width: 100%;
        }
        .blitz-ptr.ptr-pulling {
            transition: none !important; /* Zero lag during touch tracking */
            pointer-events: auto;
        }
        .blitz-ptr.ptr-refreshing {
            height: 48px !important;
            opacity: 1 !important;
            transform: translate3d(0, 0, 0) !important;
            margin-bottom: 8px;
        }
        .blitz-ptr.ptr-ready .blitz-ptr-icon-wrap {
            border-color: #1E1B4B;
            background: rgba(30, 27, 75, 0.1);
            box-shadow: 0 0 16px rgba(30, 27, 75, 0.2);
        }
        .blitz-ptr-icon-wrap {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1.5px solid rgba(30, 27, 75, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.1);
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.08s ease;
            will-change: transform;
            flex-shrink: 0;
        }
        .blitz-ptr.ptr-refreshing .blitz-ptr-spinner {
            animation: blitzPtrSpin 0.7s linear infinite !important;
        }
        @keyframes blitzPtrSpin {
            to { transform: rotate(360deg); }
        }
        .blitz-ptr-label {
            font-size: 12px;
            font-weight: 700;
            color: #1E1B4B;
            letter-spacing: 0.01em;
            font-family: inherit;
        }

        .tab-bar-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 6px 12px;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: none;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.01em;
            position: relative;
            background: none;
            border: none;
            outline: none;
            cursor: pointer;
            touch-action: manipulation !important;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            -webkit-user-select: none;
            will-change: transform, color;
            transition: transform 0.04s cubic-bezier(0.2, 0, 0, 1), color 0.06s ease;
        }

        .tab-bar-item:active {
            transform: scale(0.86) translateY(1px) !important;
            color: #FFFFFF !important;
        }

        .tab-bar-item svg {
            transition: transform 0.08s ease, color 0.06s ease;
            pointer-events: none;
        }
        .tab-bar-item span {
            pointer-events: none;
            transition: color 0.06s ease;
        }

        .tab-bar-item.active {
            color: #FFFFFF;
        }

        .tab-bar-item.active svg {
            color: #FFFFFF;
            transform: scale(1.08);
        }

        /* PVADeals-style active pill indicator */
        .tab-active-pill {
            position: absolute;
            top: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 42px;
            height: 30px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.12);
            opacity: 0;
            transition: opacity 0.12s ease;
            pointer-events: none;
            z-index: -1;
        }
        .tab-bar-item.active .tab-active-pill {
            opacity: 1;
        }

        /* â€”â€”â€” SPA Tab Panel System (GPU-composited, zero reflow) â€”â€”â€” */
        .blitz-tab-panel {
            display: none;
            flex-direction: column;
            gap: 14px;
            padding-top: 4px;
            contain: content;
        }
        .blitz-tab-panel.blitz-tab-active {
            display: flex;
        }

        .tab-fab {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #1E1B4B;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(30, 27, 75, 0.3);
            margin-top: -16px;
            transition: transform 0.15s ease;
        }

        .tab-fab:active { transform: scale(0.92); }

        /* â€”â€”â€” Reusable Components â€”â€”â€” */

        /* Section Headers */
        .section-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-tertiary);
            letter-spacing: 0.02em;
            margin-bottom: 12px;
        }

        /* Cards */
        .card {
            background: #FFFFFF;
            border: 1px solid rgba(30, 27, 75, 0.08);
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(30, 27, 75, 0.04);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .card:active {
            transform: scale(0.985);
            border-color: rgba(30, 27, 75, 0.25);
        }

        .card-body { padding: 18px; }

        /* Metric Pill */
        .metric-row {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .metric-pill {
            flex: 1;
            background: #FFFFFF;
            border: 1px solid rgba(30, 27, 75, 0.08);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);
        }

        .metric-pill-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .metric-pill-value {
            font-size: 22px;
            font-weight: 800;
            color: #1E1B4B;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.02em;
        }

        .metric-pill-sub {
            font-size: 11px;
            color: var(--text-tertiary);
        }

        /* Balance Hero Card */
        .balance-hero {
            background: #1E1B4B;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 22px 20px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(30, 27, 75, 0.18);
        }

        .balance-hero::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .balance-hero::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .balance-label {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .balance-amount {
            font-size: 34px;
            font-weight: 800;
            color: #7C3AED;
            background: #FFFFFF;
            padding: 2px 14px;
            border-radius: 12px;
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.02em;
            margin-top: 6px;
        }

        .balance-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .balance-actions {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            position: relative;
            z-index: 2;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn:active { transform: scale(0.97); }

        .btn-primary {
            background: #1E1B4B;
            color: #fff;
            padding: 12px 20px;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.2);
        }

        .btn-cta {
            background: #7C3AED;
            color: #fff;
            padding: 12px 20px;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);
        }

        .btn-ghost {
            background: #EEF1F8;
            color: #1E1B4B;
            border: 1px solid rgba(30, 27, 75, 0.1);
            padding: 12px 20px;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 12px;
            border-radius: 10px;
        }

        .btn-danger-outline {
            background: transparent;
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.25);
            padding: 12px 20px;
        }

        .btn-full { width: 100%; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .badge-success { background: rgba(124, 58, 237, 0.12); color: #7C3AED; }
        .badge-warning { background: rgba(245, 158, 11, 0.12); color: #b45309; }
        .badge-danger { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
        .badge-info { background: rgba(30, 27, 75, 0.08); color: #1E1B4B; }
        .badge-primary { background: rgba(30, 27, 75, 0.1); color: #1E1B4B; }
        .badge-neutral { background: #EEF1F8; color: var(--text-secondary); }

        /* Status dot */
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .status-dot-pulse {
            animation: dot-pulse 2s ease-in-out infinite;
        }

        @keyframes dot-pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.5); }
            50% { opacity: 0.6; box-shadow: 0 0 0 4px rgba(124, 58, 237, 0); }
        }

        /* Divider */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 16px 0;
        }

        /* List Items */
        .list-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            color: inherit;
        }

        .list-item:last-child { border-bottom: none; }

        .list-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .list-content { flex: 1; min-width: 0; }

        .list-title {
            font-size: 14px;
            font-weight: 600;
            color: #1E1B4B;
        }

        .list-subtitle {
            font-size: 12px;
            color: var(--text-tertiary);
            margin-top: 2px;
        }

        .list-trailing {
            text-align: right;
            flex-shrink: 0;
        }

        /* Session Cards */
        .session-card {
            background: #FFFFFF;
            border: 1px solid rgba(30, 27, 75, 0.1);
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(30, 27, 75, 0.04);
        }

        .session-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1E1B4B, #7C3AED);
        }

        .session-phone {
            font-size: 22px;
            font-weight: 800;
            color: #1E1B4B;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.02em;
        }

        .otp-display {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            padding: 14px;
            border-radius: 14px;
            background: #F0FDF4;
            border: 1px solid rgba(124, 58, 237, 0.3);
        }

        .otp-code {
            font-size: 28px;
            font-weight: 900;
            color: #7C3AED;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.18em;
        }

        /* Form Inputs */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1E1B4B;
            margin-bottom: 8px;
        }

        .form-select, .form-input {
            width: 100%;
            background: #EEF1F8;
            border: 1px solid rgba(30, 27, 75, 0.12);
            border-radius: 14px;
            padding: 14px 16px;
            color: #1E1B4B;
            font-size: 15px;
            font-family: inherit;
            appearance: none;
            -webkit-appearance: none;
            transition: all 0.2s ease;
        }

        .form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 40px; }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
        }

        /* Shimmer / Loading */
        .skeleton {
            background: linear-gradient(90deg, var(--bg-card) 25%, var(--bg-card-elevated) 50%, var(--bg-card) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }

        .empty-state-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 6px;
        }

        .empty-state-text {
            font-size: 13px;
            color: var(--text-tertiary);
            line-height: 1.5;
        }

        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Fade-in animation */
        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* Pull indicator */
        .scroll-content::before {
            content: '';
            display: block;
            height: 6px;
        }

        /* Payment Splash Animation */
        @keyframes splashPop {
            0% { transform: scale(0.6); opacity: 0; }
            60% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
    <?php echo $__env->yieldContent('head_styles'); ?>
</head>
<body>
    <!-- Global Native Splashscreen (plays on cold launch, fast-dismisses if seen) -->
    <?php if (isset($component)) { $__componentOriginal13b131cdff7416912ad72cc91b07a5c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal13b131cdff7416912ad72cc91b07a5c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.splashscreen','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('splashscreen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal13b131cdff7416912ad72cc91b07a5c1)): ?>
<?php $attributes = $__attributesOriginal13b131cdff7416912ad72cc91b07a5c1; ?>
<?php unset($__attributesOriginal13b131cdff7416912ad72cc91b07a5c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal13b131cdff7416912ad72cc91b07a5c1)): ?>
<?php $component = $__componentOriginal13b131cdff7416912ad72cc91b07a5c1; ?>
<?php unset($__componentOriginal13b131cdff7416912ad72cc91b07a5c1); ?>
<?php endif; ?>

    <div class="app-shell">
        <!-- Status Bar Zone -->
        <div class="status-bar"></div>

        <!-- App Bar -->
        <?php if (! empty(trim($__env->yieldContent('custom_top_bar')))): ?>
            <?php echo $__env->yieldContent('custom_top_bar'); ?>
        <?php elseif(!View::hasSection('no_header')): ?>
            <header class="app-bar">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <?php if (! empty(trim($__env->yieldContent('back_url')))): ?>
                        <a href="<?php echo $__env->yieldContent('back_url'); ?>" style="color: var(--text-white); text-decoration: none; display: flex; align-items: center; margin-left: -4px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                        </a>
                        <span class="app-bar-title" style="font-size: 18px;"><?php echo $__env->yieldContent('header_title', 'Back'); ?></span>
                    <?php else: ?>
                        <span class="app-bar-title"><?php echo $__env->yieldContent('header_title', 'Verixa'); ?></span>
                    <?php endif; ?>
                </div>

                <div class="app-bar-actions">
                    <?php echo $__env->yieldContent('header_actions'); ?>
                </div>
            </header>
        <?php endif; ?>

        <!-- Flash Messages -->
        <?php if(session('success')): ?>
            <div style="margin: 0 20px 10px; padding: 12px 14px; border-radius: 14px; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.25); color: #A78BFA; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div style="margin: 0 20px 10px; padding: 12px 14px; border-radius: 14px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.25); color: #f87171; font-size: 13px; font-weight: 600;">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <!-- Scrollable Content Area with Pull-To-Refresh -->
        <main class="scroll-content" id="blitz-scroll-content">
            <!-- Native Pull-To-Refresh Indicator Badge -->
            <div id="blitz-ptr" class="blitz-ptr" aria-live="polite">
                <div class="blitz-ptr-icon-wrap" id="blitz-ptr-icon-wrap">
                    <svg id="blitz-ptr-spinner" class="blitz-ptr-spinner" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E1B4B" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.19"/>
                    </svg>
                </div>
                <span class="blitz-ptr-label" id="blitz-ptr-label">Pull to refresh</span>
            </div>

            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <!-- Bottom Tab Bar (PVADeals 5-Tab Navigation: Dashboard | STR | LTR | SMS | More) -->
        <nav class="tab-bar" id="blitz-tab-bar">
            <?php
                $currentTab = request()->query('tab', 'dashboard');
                // Normalize tab aliases
                if (in_array($currentTab, ['home', 'index'])) $currentTab = 'dashboard';
                if (in_array($currentTab, ['sms', 'my-numbers', 'sessions'])) $currentTab = 'numbers';
                if (in_array($currentTab, ['ltr', 'esim'])) $currentTab = 'rent';
                if (in_array($currentTab, ['str', 'get-number'])) $currentTab = 'buy';
                if (in_array($currentTab, ['settings', 'more', 'profile'])) $currentTab = 'account';
            ?>
            <!-- 1. Dashboard -->
            <button type="button" class="tab-bar-item <?php echo e($currentTab === 'dashboard' ? 'active' : ''); ?> blitz-btn" data-tab="dashboard"
                onclick="switchTab('dashboard', this, event)" aria-label="Dashboard">
                <span class="tab-active-pill"></span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="2"/>
                    <rect x="14" y="3" width="7" height="5" rx="2"/>
                    <rect x="14" y="12" width="7" height="9" rx="2"/>
                    <rect x="3" y="16" width="7" height="5" rx="2"/>
                </svg>
                <span>Dashboard</span>
            </button>

            <!-- 2. STR (Short-Term Rental / Buy Number) -->
            <button type="button" class="tab-bar-item <?php echo e($currentTab === 'buy' ? 'active' : ''); ?> blitz-btn" data-tab="buy"
                onclick="switchTab('buy', this, event)" aria-label="Short-Term Rental">
                <span class="tab-active-pill"></span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="3"/>
                    <path d="M12 18h.01"/>
                    <path d="M13 7l-3 4h4l-2 4"/>
                </svg>
                <span>STR</span>
            </button>

            <!-- 3. LTR (Long-Term Rental) -->
            <button type="button" class="tab-bar-item <?php echo e($currentTab === 'rent' ? 'active' : ''); ?> blitz-btn" data-tab="rent"
                onclick="switchTab('rent', this, event)" aria-label="Long-Term Rental">
                <span class="tab-active-pill"></span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="3"/>
                    <path d="M16 2v4M8 2v4M3 10h18"/>
                    <path d="M8 15h3l2 2 3-3"/>
                </svg>
                <span>LTR</span>
            </button>

            <!-- 4. SMS (My Numbers / Verification Sessions) -->
            <button type="button" class="tab-bar-item <?php echo e($currentTab === 'numbers' ? 'active' : ''); ?> blitz-btn" data-tab="numbers"
                onclick="switchTab('numbers', this, event)" aria-label="SMS Sessions">
                <span class="tab-active-pill"></span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    <path d="M8 12h.01M12 12h.01M16 12h.01"/>
                </svg>
                <span>SMS</span>
            </button>

            <!-- 5. More (Account + Wallet Combined) -->
            <button type="button" class="tab-bar-item <?php echo e($currentTab === 'account' ? 'active' : ''); ?> blitz-btn" data-tab="account"
                onclick="switchTab('account', this, event)" aria-label="More Options">
                <span class="tab-active-pill"></span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="6" x2="20" y2="6"/>
                    <line x1="4" y1="12" x2="20" y2="12"/>
                    <line x1="4" y1="18" x2="20" y2="18"/>
                    <circle cx="8" cy="6" r="2" fill="currentColor"/>
                    <circle cx="16" cy="12" r="2" fill="currentColor"/>
                    <circle cx="10" cy="18" r="2" fill="currentColor"/>
                </svg>
                <span>More</span>
            </button>
        </nav>

        <!-- Secondary Modal: Notifications -->
        <div id="notifications-modal" class="blitz-modal" role="dialog" aria-modal="true" aria-label="Notifications">
            <div style="background: #1E1B4B; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; color: #fff;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <span style="font-size: 16px; font-weight: 800; color: #FFFFFF;">Notifications</span>
                </div>
                <button type="button" onclick="closeBlitzModal('notifications-modal')" class="blitz-btn" style="background: rgba(255,255,255,0.12); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px;">
                <?php if(isset($notifications) && count($notifications) > 0): ?>
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 12px 14px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 3px;"><?php echo e($notif->title ?? 'System Notice'); ?></div>
                            <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.4;"><?php echo e($notif->body ?? $notif->message); ?></div>
                            <div style="font-size: 10.5px; color: var(--text-tertiary); margin-top: 6px;"><?php echo e($notif->created_at?->diffForHumans()); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </div>
                        <div class="empty-state-title" style="color: var(--text-primary);">No Notifications</div>
                        <div class="empty-state-text">You're all caught up! Verification and payment alerts will show here.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Secondary Modal: Security & PIN Management -->
        <div id="security-pin-modal" class="blitz-modal" role="dialog" aria-modal="true" aria-label="Security and PIN">
            <div style="background: #1E1B4B; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; color: #fff;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <span style="font-size: 16px; font-weight: 800; color: #FFFFFF;">Security & PIN</span>
                </div>
                <button type="button" onclick="closeBlitzModal('security-pin-modal')" class="blitz-btn" style="background: rgba(255,255,255,0.12); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="flex: 1; overflow-y: auto; padding: 18px; display: flex; flex-direction: column; gap: 16px;">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 16px;">
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">4-Digit App Security PIN</div>
                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 14px;">Protect your wallet balance and numbers with a local passcode when launching the app.</div>
                    
                    <div style="display: flex; justify-content: center; gap: 12px; margin-bottom: 16px;" id="pin-dots-container">
                        <input type="password" maxlength="1" class="pin-digit" id="pin-d-1" style="width: 44px; height: 48px; border-radius: 12px; border: 1.5px solid var(--border); background: var(--bg-input); text-align: center; font-size: 20px; font-weight: 800; color: var(--text-primary);" oninput="handlePinInput(1, this)">
                        <input type="password" maxlength="1" class="pin-digit" id="pin-d-2" style="width: 44px; height: 48px; border-radius: 12px; border: 1.5px solid var(--border); background: var(--bg-input); text-align: center; font-size: 20px; font-weight: 800; color: var(--text-primary);" oninput="handlePinInput(2, this)">
                        <input type="password" maxlength="1" class="pin-digit" id="pin-d-3" style="width: 44px; height: 48px; border-radius: 12px; border: 1.5px solid var(--border); background: var(--bg-input); text-align: center; font-size: 20px; font-weight: 800; color: var(--text-primary);" oninput="handlePinInput(3, this)">
                        <input type="password" maxlength="1" class="pin-digit" id="pin-d-4" style="width: 44px; height: 48px; border-radius: 12px; border: 1.5px solid var(--border); background: var(--bg-input); text-align: center; font-size: 20px; font-weight: 800; color: var(--text-primary);" oninput="handlePinInput(4, this)">
                    </div>

                    <button type="button" onclick="saveAppPin()" class="blitz-btn" style="width: 100%; background: #7C3AED; color: #fff; border: none; border-radius: 12px; height: 44px; font-size: 14px; font-weight: 700; cursor: pointer;">
                        Save PIN Passcode
                    </button>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-primary);">Biometric Unlock</div>
                        <div style="font-size: 11.5px; color: var(--text-secondary);">Use Fingerprint or Face ID when supported</div>
                    </div>
                    <input type="checkbox" id="biometric-toggle" style="width: 20px; height: 20px; accent-color: #7C3AED; cursor: pointer;" onchange="toggleBiometrics(this.checked)">
                </div>
            </div>
        </div>

        <!-- Secondary Modal: Support -->
        <div id="support-modal" class="blitz-modal" role="dialog" aria-modal="true" aria-label="Support Desk">
            <div style="background: #1E1B4B; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; color: #fff;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <span style="font-size: 16px; font-weight: 800; color: #FFFFFF;">Support & Help Desk</span>
                </div>
                <button type="button" onclick="closeBlitzModal('support-modal')" class="blitz-btn" style="background: rgba(255,255,255,0.12); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="flex: 1; overflow-y: auto; padding: 18px; display: flex; flex-direction: column; gap: 14px;">
                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 16px;">
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">How Verification Works</div>
                    <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.5;">
                        1. Select country & service from <strong>Buy Number</strong>.<br>
                        2. A temporary hold is placed on your wallet balance (you are never permanently charged unless an SMS code arrives).<br>
                        3. Use the phone number on WhatsApp, Telegram, Google, etc.<br>
                        4. When the code arrives, copy it with 1-tap and the transaction completes.<br>
                        5. If cancelled or timed out (20 mins), your balance is automatically released.
                    </div>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 16px;">
                    <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">Contact Help Desk</div>
                    <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 12px;">Need help with a payment, country route, or API access?</div>
                    <a href="mailto:support@Verixa.com" style="display: flex; align-items: center; justify-content: center; height: 42px; border-radius: 12px; background: #1E1B4B; color: #fff; text-decoration: none; font-size: 13px; font-weight: 700;">
                        Email Support Team
                    </a>
                </div>
            </div>
        </div>

        <!-- Secondary Modal: Terms & Privacy -->
        <div id="terms-privacy-modal" class="blitz-modal" role="dialog" aria-modal="true" aria-label="Terms and Privacy">
            <div style="background: #1E1B4B; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                <span style="font-size: 16px; font-weight: 800; color: #FFFFFF;" id="terms-modal-title">Terms & Privacy</span>
                <button type="button" onclick="closeBlitzModal('terms-privacy-modal')" class="blitz-btn" style="background: rgba(255,255,255,0.12); border: none; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="flex: 1; overflow-y: auto; padding: 18px; font-size: 12.5px; color: var(--text-secondary); line-height: 1.6;">
                <p style="margin-bottom: 12px;"><strong>Virtual Number Usage Policy:</strong> Virtual numbers provided via Verixa are for legitimate account verification and SMS testing purposes only. Abuse or fraudulent activity results in immediate account restriction.</p>
                <p style="margin-bottom: 12px;"><strong>Refund Guarantee:</strong> If no SMS is received within the rental duration, your wallet hold is 100% automatically refunded to your available balance.</p>
                <p><strong>Privacy:</strong> We encrypt and safeguard all account credentials and do not store incoming SMS content beyond necessary verification retention rules.</p>
            </div>
        </div>
    </div>

    <script>
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        //  BLITZ ZERO-LAG SPLASH & TAP ENGINE (0ms Touch Response)
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

        // Instant GPU splash ripple effect at exact touch coordinates
        function blitzCreateSplash(e, targetEl) {
            if (!targetEl) return;
            const rect = targetEl.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'blitz-ripple';

            let x, y;
            if (e && (e.clientX || (e.touches && e.touches[0]))) {
                const clientX = e.clientX || e.touches[0].clientX;
                const clientY = e.clientY || e.touches[0].clientY;
                x = clientX - rect.left;
                y = clientY - rect.top;
            } else {
                x = rect.width / 2;
                y = rect.height / 2;
            }

            const size = Math.max(rect.width, rect.height) * 1.5;
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x - size / 2}px`;
            ripple.style.top = `${y - size / 2}px`;

            targetEl.appendChild(ripple);
            setTimeout(() => { if (ripple.parentNode) ripple.parentNode.removeChild(ripple); }, 300);
        }

        // Global capture listener: immediately gives EVERY button/pill a 0ms splash & haptic kick
        document.addEventListener('pointerdown', function(e) {
            const btn = e.target.closest('button, .blitz-btn, .tab-bar-item, .service-chip, .quick-amt-pill, [role="button"]');
            if (btn) {
                try { if (navigator.vibrate) navigator.vibrate(10); } catch(err) {}
                blitzCreateSplash(e, btn);
            }
        }, { passive: true, capture: true });

        // Blitz Zero-Lag Tap Engine (Cancels 300ms mobile delay and provides instant visual/haptic feedback)
        window.blitzTap = function(e, callback) {
            if (e) {
                const target = e.currentTarget || e.target;
                try { if (navigator.vibrate) navigator.vibrate(10); } catch(err) {}
                if (target) blitzCreateSplash(e, target);
            }
            if (typeof callback === 'function') {
                callback(e);
            }
        };

        // Blitz Ultra-Fast Logout Engine: 0ms visual reaction, instant async teardown, direct landing
        window.blitzLogout = async function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            const btn = (e && e.currentTarget) 
                || (e && e.target ? e.target.closest('button, .blitz-btn') : null)
                || document.getElementById('logout-btn')
                || document.querySelector('.blitz-logout-btn');

            if (btn) {
                btn.disabled = true;
                btn.style.transform = 'scale(0.96)';
                btn.style.opacity = '0.9';
                btn.innerHTML = '<span style="display:inline-block;width:15px;height:15px;border:2px solid rgba(239,68,68,0.3);border-top-color:#EF4444;border-radius:50%;animation:blitzSpin 0.5s linear infinite;vertical-align:middle;margin-right:8px;"></span><span>Signing Out...</span>';
            }

            try { if (navigator.vibrate) navigator.vibrate([15, 30]); } catch(err) {}

            // Immediate teardown of client-side auth / cache
            try {
                sessionStorage.clear();
                localStorage.removeItem('segyric_pending_payment');
                localStorage.removeItem('verixa_pending_payment');
                localStorage.removeItem('Verixa_pending_payment');
                localStorage.removeItem('pending_deposit_ref');
                localStorage.removeItem('pending_deposit_amt');
                localStorage.removeItem('pending_deposit_time');
            } catch(err) {}

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const loginUrl = "<?php echo e(route('login', [], false)); ?>";

            try {
                const response = await fetch("<?php echo e(route('logout', [], false)); ?>", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ platform: 'ios', source: 'mobile' })
                });

                const data = await response.json().catch(() => ({}));
                window.location.replace(data.redirect || loginUrl);
            } catch(err) {
                window.location.replace(loginUrl);
            }
        };

        // Copy to clipboard with haptic-style feedback
        function copyToClipboard(text, btn) {
            try { if (navigator.vibrate) navigator.vibrate(12); } catch(err) {}
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showCopied(btn)).catch(() => fallbackCopy(text, btn));
            } else {
                fallbackCopy(text, btn);
            }
        }

        function showCopied(btn) {
            if (!btn) return;
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Copied';
            btn.style.borderColor = 'rgba(16, 185, 129, 0.4)';
            btn.style.color = '#34d399';
            setTimeout(() => { btn.innerHTML = orig; btn.style.borderColor = ''; btn.style.color = ''; }, 1500);
        }

        function fallbackCopy(text, btn) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try { document.execCommand('copy'); } catch(e) {}
            document.body.removeChild(ta);
            showCopied(btn);
        }

        // CSRF token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        //  BLITZ NATIVE PULL-TO-REFRESH ENGINE (iOS/Android Fluid Physics)
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        (function() {
            let startY = 0;
            let currentY = 0;
            let isPulling = false;
            let isRefreshing = false;
            const threshold = 65; // px pull distance required to trigger refresh
            const maxPull = 92;   // max drag distance with damping

            const scrollEl = document.getElementById('blitz-scroll-content') || document.querySelector('.scroll-content');
            const ptrEl = document.getElementById('blitz-ptr');
            const ptrLabel = document.getElementById('blitz-ptr-label');
            const ptrIconWrap = document.getElementById('blitz-ptr-icon-wrap');

            if (!scrollEl || !ptrEl) return;

            let tickPlayed = false;

            scrollEl.addEventListener('touchstart', function(e) {
                if (isRefreshing) return;
                // Only allow pull-to-refresh when at the top of the scroll container
                if (scrollEl.scrollTop <= 2) {
                    startY = e.touches[0].pageY;
                    currentY = startY;
                    isPulling = true;
                    tickPlayed = false;
                } else {
                    isPulling = false;
                }
            }, { passive: true });

            scrollEl.addEventListener('touchmove', function(e) {
                if (!isPulling || isRefreshing) return;

                currentY = e.touches[0].pageY;
                const diff = currentY - startY;

                if (diff > 0 && scrollEl.scrollTop <= 2) {
                    // Rubber-band resistance: logarithmic damping curve
                    const pullDist = Math.min(maxPull, Math.pow(diff, 0.82) * 1.35);

                    ptrEl.classList.add('ptr-pulling');
                    ptrEl.style.height = `${pullDist}px`;
                    ptrEl.style.opacity = `${Math.min(1, pullDist / 35)}`;
                    ptrEl.style.transform = `translate3d(0, 0, 0)`;

                    // Rotate spinner icon proportional to pull depth
                    const rotation = (pullDist / threshold) * 280;
                    if (ptrIconWrap) ptrIconWrap.style.transform = `rotate(${rotation}deg)`;

                    if (pullDist >= threshold) {
                        if (!tickPlayed) {
                            try { if (navigator.vibrate) navigator.vibrate(15); } catch(err) {}
                            tickPlayed = true;
                        }
                        if (ptrLabel) ptrLabel.textContent = 'Release to refresh';
                        ptrEl.classList.add('ptr-ready');
                    } else {
                        tickPlayed = false;
                        if (ptrLabel) ptrLabel.textContent = 'Pull down to refresh';
                        ptrEl.classList.remove('ptr-ready');
                    }

                    // Cancel native browser overscroll pull
                    if (e.cancelable && diff > 15) {
                        e.preventDefault();
                    }
                } else {
                    ptrEl.style.height = '0px';
                    ptrEl.style.opacity = '0';
                    ptrEl.classList.remove('ptr-pulling', 'ptr-ready');
                }
            }, { passive: false });

            const endPull = async function() {
                if (!isPulling || isRefreshing) return;
                isPulling = false;
                ptrEl.classList.remove('ptr-pulling');

                const diff = currentY - startY;
                const pullDist = Math.min(maxPull, Math.pow(diff, 0.82) * 1.35);

                if (diff > 0 && pullDist >= threshold) {
                    isRefreshing = true;
                    ptrEl.classList.remove('ptr-ready');
                    ptrEl.classList.add('ptr-refreshing');
                    ptrEl.style.height = '48px';
                    ptrEl.style.opacity = '1';
                    if (ptrLabel) ptrLabel.textContent = 'Refreshing live data...';
                    try { if (navigator.vibrate) navigator.vibrate([15, 30, 15]); } catch(err) {}

                    await triggerLiveRefresh();

                    if (ptrLabel) ptrLabel.textContent = 'Updated âœ“';
                    try { if (navigator.vibrate) navigator.vibrate(12); } catch(err) {}

                    setTimeout(() => {
                        ptrEl.classList.remove('ptr-refreshing');
                        ptrEl.style.height = '0px';
                        ptrEl.style.opacity = '0';
                        if (ptrIconWrap) ptrIconWrap.style.transform = 'none';
                        setTimeout(() => {
                            isRefreshing = false;
                            if (ptrLabel) ptrLabel.textContent = 'Pull to refresh';
                        }, 250);
                    }, 550);
                } else {
                    ptrEl.style.height = '0px';
                    ptrEl.style.opacity = '0';
                    if (ptrIconWrap) ptrIconWrap.style.transform = 'none';
                }
            };

            scrollEl.addEventListener('touchend', endPull, { passive: true });
            scrollEl.addEventListener('touchcancel', endPull, { passive: true });

            // Universal live refresh executor
            async function triggerLiveRefresh() {
                try {
                    // 1. Live wallet balance refresh
                    const balRes = await fetch("<?php echo e(url('/wallet/balance')); ?>", {
                        headers: { 'Accept': 'application/json', 'Cache-Control': 'no-cache' },
                        credentials: 'same-origin'
                    });
                    if (balRes.ok) {
                        const balData = await balRes.json();
                        if (balData.status && balData.balance !== undefined) {
                            if (typeof window.updateBalanceInPlace === 'function') {
                                window.updateBalanceInPlace(balData.balance, balData.symbol);
                            }
                        }
                    }

                    // 2. PVADeals catalog refresh if available
                    if (typeof pvFetchCatalog === 'function') {
                        await pvFetchCatalog();
                    }

                    // 3. Keep indicator visible for a silky feedback duration
                    await new Promise(r => setTimeout(r, 450));
                } catch(e) {
                    console.warn('Pull-to-refresh sync:', e);
                }
            }

            // Expose globally for manual or programmatic triggers
            window.blitzPerformRefresh = triggerLiveRefresh;
        })();

        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        //  BLITZ SPA TAB SWITCH ENGINE â€” 0ms instant tab switching
        //  Fires on pointerdown (before click), pure DOM class toggle
        //  Zero HTTP requests, zero WebView reloads
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        let _blitzActiveTab = '<?php echo e($currentTab ?? "dashboard"); ?>';
        let _blitzSwitching = false;

        function normalizeTabName(tab) {
            tab = (tab || 'dashboard').toLowerCase();
            if (tab === 'home' || tab === 'index') return 'dashboard';
            if (tab === 'deposit') return 'wallet';
            if (tab === 'transactions') return 'wallet';
            if (tab === 'sms' || tab === 'my-numbers' || tab === 'sessions') return 'numbers';
            if (tab === 'esim') return 'rent';
            if (tab === 'settings' || tab === 'profile' || tab === 'more') return 'account';
            return tab;
        }

        function switchTab(tabName, btnEl, evt) {
            tabName = normalizeTabName(tabName);

            if (evt) {
                const target = evt.currentTarget || evt.target;
                const now = Date.now();
                if (evt.type === 'pointerdown' || evt.type === 'touchstart') {
                    if (target) target._blitzLastTouch = now;
                } else if (evt.type === 'click' && target && target._blitzLastTouch) {
                    if (now - target._blitzLastTouch < 450) {
                        if (evt.cancelable) evt.preventDefault();
                        return;
                    }
                }
            }

            // CRITICAL: dismiss open bottom sheets, modals and reset scroll locks when tapping any tab
            if (typeof pvCloseWebsiteSheet === 'function') {
                pvCloseWebsiteSheet();
            }
            if (typeof window.pvCloseWebsiteSheet === 'function') {
                window.pvCloseWebsiteSheet();
            }
            document.querySelectorAll('.blitz-modal.blitz-open').forEach(m => m.classList.remove('blitz-open'));
            document.querySelectorAll('.pv-sheet-container.open, .pv-sheet-backdrop.open').forEach(s => s.classList.remove('open'));
            document.body.style.overflow = '';
            window._returnTabAfterSheet = null;

            // Clear switching lock if it ever exceeded 200ms
            if (_blitzSwitching) {
                _blitzSwitching = false;
            }

            const targetPanel = document.getElementById('blitz-panel-' + tabName);
            if (!targetPanel) {
                // If not on the SPA home page, instantly route there with tab param
                window.location.href = "<?php echo e(route('mobile.home', [], false)); ?>?tab=" + tabName;
                return;
            }

            if (tabName === _blitzActiveTab && targetPanel.classList.contains('blitz-tab-active')) {
                // Tapping active tab scrolls smoothly to top
                const scrollArea = document.querySelector('.scroll-content');
                if (scrollArea) scrollArea.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            _blitzSwitching = true;

            try {
                // 1. Haptic micro-feedback (10ms vibration)
                try { if (navigator.vibrate) navigator.vibrate(10); } catch(e) {}

                // 2. Instant active state on tab bar (0ms visual feedback)
                const allBtns = document.querySelectorAll('#blitz-tab-bar .tab-bar-item');
                for (let i = 0; i < allBtns.length; i++) {
                    allBtns[i].classList.remove('active');
                }
                const activeBtn = btnEl || document.querySelector(`#blitz-tab-bar [data-tab="${tabName}"]`) || (tabName === 'wallet' ? document.querySelector('#blitz-tab-bar [data-tab="dashboard"]') : null);
                if (activeBtn) activeBtn.classList.add('active');

                // 3. Instant tab panel swap (pure CSS class toggle, no reflow)
                const allPanels = document.querySelectorAll('.blitz-tab-panel');
                for (let i = 0; i < allPanels.length; i++) {
                    allPanels[i].classList.remove('blitz-tab-active');
                }
                targetPanel.classList.add('blitz-tab-active');

                // 4. Scroll to top instantly
                const scrollArea = document.querySelector('.scroll-content');
                if (scrollArea) scrollArea.scrollTop = 0;

                _blitzActiveTab = tabName;

                // Update URL query string without reloading page
                try {
                    const cleanUrl = new URL(window.location);
                    cleanUrl.searchParams.set('tab', tabName);
                    cleanUrl.searchParams.delete('auth_token');
                    window.history.replaceState({}, document.title, cleanUrl.pathname + cleanUrl.search);
                } catch(e) {}
            } finally {
                setTimeout(function() { _blitzSwitching = false; }, 60);
            }
        }

        // Modal Helpers
        window.openBlitzModal = function(modalId) {
            const m = document.getElementById(modalId);
            if (m) {
                m.classList.add('blitz-open');
                try { if (navigator.vibrate) navigator.vibrate(10); } catch(e) {}
            }
        };

        window.closeBlitzModal = function(modalId) {
            const m = document.getElementById(modalId);
            if (m) {
                m.classList.remove('blitz-open');
                try { if (navigator.vibrate) navigator.vibrate(8); } catch(e) {}
            }
        };

        window.openNotificationsModal = function() {
            window.openBlitzModal('notifications-modal');
        };

        window.openSecurityModal = function() {
            window.openBlitzModal('security-pin-modal');
        };

        window.openSupportModal = function() {
            window.openBlitzModal('support-modal');
        };

        window.openTermsModal = function() {
            window.openBlitzModal('terms-privacy-modal');
        };

        // Theme Switcher & Synchronizer (Light/Dark mode matching Desktop)
        window.applyTheme = function(isDark) {
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.classList.add('dark');
                document.documentElement.classList.add('dark-theme');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#0B132B');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.remove('dark-theme');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#1E1B4B');
            }
            const icon = document.getElementById('theme-toggle-icon');
            if (icon) {
                if (isDark) {
                    icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
                } else {
                    icon.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
                }
            }
            const accountThemeLabel = document.getElementById('account-theme-label');
            if (accountThemeLabel) {
                accountThemeLabel.textContent = isDark ? 'Dark' : 'Light';
            }
        };

        window.toggleTheme = function() {
            const current = document.documentElement.getAttribute('data-theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            const nextIsDark = current !== 'dark';
            window.applyTheme(nextIsDark);
            localStorage.setItem('dark_mode', nextIsDark);
            localStorage.setItem('Verixa_theme', nextIsDark ? 'dark' : 'light');
            localStorage.setItem('app-theme', nextIsDark ? 'dark' : 'light');
        };

        // Initialize Theme from localStorage
        (function() {
            const saved = localStorage.getItem('Verixa_theme') || localStorage.getItem('app-theme') || (localStorage.getItem('dark_mode') === 'true' ? 'dark' : null);
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const useDark = (saved === 'dark') || (!saved && prefersDark);
            window.applyTheme(useDark);
        })();

        // PIN Security Handlers
        window.handlePinInput = function(idx, el) {
            if (el.value.length >= 1 && idx < 4) {
                const next = document.getElementById('pin-d-' + (idx + 1));
                if (next) next.focus();
            }
        };

        window.saveAppPin = function() {
            const p1 = document.getElementById('pin-d-1')?.value || '';
            const p2 = document.getElementById('pin-d-2')?.value || '';
            const p3 = document.getElementById('pin-d-3')?.value || '';
            const p4 = document.getElementById('pin-d-4')?.value || '';
            const pin = p1 + p2 + p3 + p4;
            if (pin.length === 4) {
                localStorage.setItem('Verixa_app_pin', pin);
                alert('App PIN passcode saved successfully.');
                closeBlitzModal('security-pin-modal');
            } else {
                alert('Please enter a 4-digit numeric PIN.');
            }
        };

        window.toggleBiometrics = function(enabled) {
            localStorage.setItem('Verixa_biometrics_enabled', enabled ? '1' : '0');
        };

        // Clean auth_token from address bar for a seamless URL
        if (window.location.search.includes('auth_token=')) {
            const cleanUrl = new URL(window.location);
            cleanUrl.searchParams.delete('auth_token');
            window.history.replaceState({}, document.title, cleanUrl.pathname + (cleanUrl.search ? cleanUrl.search : ''));
        }

        // Global Simple Page Loading Progress Indicator for Mobile
        (function() {
            const loader = document.getElementById('global-page-loader');
            if (!loader) return;

            let progressTimer = null;
            let currentWidth = 0;

            window.startPageLoading = function() {
                if (progressTimer) clearInterval(progressTimer);
                currentWidth = 20;
                loader.style.opacity = '1';
                loader.style.width = currentWidth + '%';

                progressTimer = setInterval(() => {
                    if (currentWidth < 85) {
                        currentWidth += Math.random() * 18;
                        if (currentWidth > 85) currentWidth = 85;
                        loader.style.width = currentWidth + '%';
                    }
                }, 120);
            };

            window.finishPageLoading = function() {
                if (progressTimer) clearInterval(progressTimer);
                loader.style.width = '100%';
                setTimeout(() => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.width = '0%';
                    }, 300);
                }, 150);
            };

            window.addEventListener('load', () => window.finishPageLoading());
            window.addEventListener('pageshow', () => {
                loader.style.opacity = '0';
                loader.style.width = '0%';
                if (progressTimer) clearInterval(progressTimer);
            });

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                const target = link.getAttribute('target');
                const download = link.getAttribute('download');
                const noLoader = link.hasAttribute('data-no-loader');

                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank' || download !== null || noLoader) {
                    return;
                }

                window.startPageLoading();
            }, true);

            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (form.hasAttribute('data-no-loader')) return;
                window.startPageLoading();

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.style.opacity = '0.75';
                    submitBtn.style.pointerEvents = 'none';
                }
            }, true);
        })();
    </script>

    <!-- Global Page Loader for Mobile Shell -->
    <div id="global-page-loader" style="position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(90deg, #7C3AED, #06B6D4, #6366F1); box-shadow: 0 0 12px rgba(16, 185, 129, 0.8), 0 0 6px rgba(6, 182, 212, 0.6); z-index: 999999; pointer-events: none; transition: width 0.25s ease-out, opacity 0.3s ease; opacity: 0;"></div>

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
         IN-APP PAYMENT SDK (Paystack v1 Inline â€” 100% In-App Webview Modal)
         Never triggers Safari or external browser redirection
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <!-- Paystack Inline SDK is loaded on-demand via ensurePaystackInlineLoaded() on user tap -->

    <?php
        $inappUser = auth()->user();
        $inappWallet = $inappUser ? ($inappUser->wallet ?? app(\App\Application\Services\WalletService::class)->getOrCreateUserWallet($inappUser)) : null;
        $inappCurrencySymbol = $inappWallet?->currency?->symbol ?? 'â‚¦';
        $inappCurrencyCode = $inappWallet?->currency?->code ?? 'NGN';
        $inappPaystackKey = (string) config('services.paystack.public_key', '');
    ?>

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
         1. INSTANT DEPOSIT SCREEN (Preloaded Bottom Sheet Modal â€” No Full Page Reload)
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <div id="native-deposit-screen-modal" style="display: none; position: fixed; inset: 0; z-index: 999980; background: #07101F; align-items: stretch; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
        <div id="native-deposit-sheet" style="width: 100%; max-width: 100%; height: 100%; height: 100vh; height: 100dvh; max-height: 100%; background: linear-gradient(180deg, #07101F 0%, #0B192E 50%, #1E1B4B 100%); border-radius: 0; display: flex; flex-direction: column; overflow-y: auto; -webkit-overflow-scrolling: touch; box-shadow: none; transform: translateY(100%); transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1); padding-top: max(16px, env(safe-area-inset-top, 16px)); padding-bottom: max(24px, env(safe-area-inset-bottom, 24px)); box-sizing: border-box; color: #FFFFFF; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            
            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 18px 14px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(7, 16, 31, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                <button type="button" onclick="closeDepositModal()" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); color: #FFFFFF; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;" aria-label="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <span style="font-size: 17px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.01em;">Deposit</span>
                <div style="width: 36px;"></div>
            </div>

            <!-- Content Body -->
            <div style="padding: 20px 18px; display: flex; flex-direction: column; gap: 22px; max-width: 520px; width: 100%; margin: 0 auto; box-sizing: border-box;">
                <!-- Amount Section -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: rgba(255, 255, 255, 0.75); letter-spacing: -0.01em;">
                        Enter amount (<span id="inapp-currency-symbol"><?php echo e($inappCurrencySymbol); ?></span>)
                    </label>
                    <div style="position: relative; width: 100%; background: rgba(30, 27, 75, 0.35); border: 1.5px solid rgba(255, 255, 255, 0.12); border-radius: 14px; height: 56px; display: flex; align-items: center; padding: 0 16px; box-sizing: border-box; transition: border-color 0.15s ease;">
                        <span style="font-size: 18px; font-weight: 700; color: #7C3AED; margin-right: 8px; font-family: 'JetBrains Mono', monospace;">
                            <?php echo e($inappCurrencySymbol); ?>

                        </span>
                        <input 
                            type="number" 
                            id="native-deposit-amount-input" 
                            value="2000" 
                            min="100" 
                            step="100" 
                            placeholder="Enter amount"
                            style="width: 100%; background: transparent; border: none; outline: none; font-size: 18px; font-weight: 700; color: #FFFFFF; font-family: 'JetBrains Mono', monospace;"
                            oninput="updateInAppAmountLabel()"
                        />
                    </div>

                    <!-- Presets -->
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 4px;">
                        <button type="button" class="inapp-preset-btn blitz-btn" onclick="setInAppPreset(1000, this)" style="padding: 10px 0; border-radius: 12px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(30, 27, 75, 0.4); color: rgba(255, 255, 255, 0.85); cursor: pointer; text-align: center; font-family: 'JetBrains Mono', monospace;"><?php echo e($inappCurrencySymbol); ?>1,000</button>
                        <button type="button" class="inapp-preset-btn blitz-btn active-preset" onclick="setInAppPreset(2000, this)" style="padding: 10px 0; border-radius: 12px; font-size: 12.5px; font-weight: 700; border: 1.5px solid #7C3AED; background: rgba(16, 185, 129, 0.16); color: #7C3AED; cursor: pointer; text-align: center; font-family: 'JetBrains Mono', monospace;"><?php echo e($inappCurrencySymbol); ?>2,000</button>
                        <button type="button" class="inapp-preset-btn blitz-btn" onclick="setInAppPreset(5000, this)" style="padding: 10px 0; border-radius: 12px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(30, 27, 75, 0.4); color: rgba(255, 255, 255, 0.85); cursor: pointer; text-align: center; font-family: 'JetBrains Mono', monospace;"><?php echo e($inappCurrencySymbol); ?>5,000</button>
                        <button type="button" class="inapp-preset-btn blitz-btn" onclick="setInAppPreset(10000, this)" style="padding: 10px 0; border-radius: 12px; font-size: 12.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(30, 27, 75, 0.4); color: rgba(255, 255, 255, 0.85); cursor: pointer; text-align: center; font-family: 'JetBrains Mono', monospace;"><?php echo e($inappCurrencySymbol); ?>10K</button>
                    </div>
                    <div id="inapp-amount-error" style="display: none; color: #EF4444; font-size: 12.5px; font-weight: 600; margin-top: 4px;"></div>
                </div>

                <!-- Payment Method (Paystack Only â€” Always Selected) -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="font-size: 13.5px; font-weight: 600; color: rgba(255, 255, 255, 0.75); letter-spacing: -0.01em;">
                        Payment method
                    </label>

                    <!-- Paystack Card (Always Selected, Only Option) -->
                    <div id="inapp-gateway-card-paystack" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-radius: 16px; background: rgba(30, 27, 75, 0.45); border: 1.5px solid #7C3AED; cursor: default; box-shadow: 0 4px 16px rgba(0,0,0,0.25);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.16); border: 1px solid rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#7C3AED"><path d="M4 4h16v3H4zm0 6.5h16v3H4zm0 6.5h10v3H4z"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 15px; font-weight: 700; color: #FFFFFF;">Paystack</div>
                                <div style="font-size: 11.5px; color: rgba(255,255,255,0.55);">Cards, Bank Transfer, USSD, OPay</div>
                            </div>
                        </div>
                        <div style="width: 22px; height: 22px; border-radius: 50%; background: #7C3AED; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div style="font-size: 12px; color: rgba(255, 255, 255, 0.5); text-align: center; margin-top: -2px;">
                    A secure in-app payment sheet will slide up to complete your payment.
                </div>

                <!-- Fund Wallet CTA -->
                <button type="button" id="inapp-fund-wallet-btn" onclick="startCompletePaymentFlow()" class="blitz-btn" style="width: 100%; height: 54px; background: linear-gradient(135deg, #7C3AED 0%, #059669 100%); color: #FFFFFF; border: none; border-radius: 16px; font-size: 16px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);">
                    <span id="inapp-fund-wallet-btn-label">Fund Wallet (<?php echo e($inappCurrencySymbol); ?>2,000)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
         2. "COMPLETE PAYMENT" BOTTOM SHEET (Animates Up & Expands Full Screen)
         â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
    <div id="native-complete-payment-modal" style="display: none; position: fixed; inset: 0; z-index: 999990; background: rgba(0, 0, 0, 0.82); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); align-items: flex-end; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
        <div id="native-complete-payment-sheet" style="width: 100%; max-width: 520px; height: 94vh; max-height: 94vh; background: #FFFFFF; border-top-left-radius: 24px; border-top-right-radius: 24px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 -16px 48px rgba(0,0,0,0.6); transform: translateY(100%); transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);">
            
            <!-- Sheet Header Bar -->
            <div style="height: 54px; padding: 0 16px; background: #121212; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; border-top-left-radius: 24px; border-top-right-radius: 24px; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <button type="button" onclick="closeCompletePaymentSheet()" style="background: rgba(255,255,255,0.08); color: #FFFFFF; border: none; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;" aria-label="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <div style="display: flex; align-items: center; gap: 7px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#7C3AED"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    <span style="font-size: 15px; font-weight: 800; color: #FFFFFF; letter-spacing: -0.01em;">Complete Payment</span>
                </div>

                <div id="complete-payment-amount-pill" style="font-size: 12px; font-weight: 800; color: #FF5722; font-family: 'JetBrains Mono', monospace; background: rgba(255,87,34,0.12); padding: 4px 10px; border-radius: 8px;">
                    <?php echo e($inappCurrencySymbol); ?>2,000
                </div>
            </div>

            <!-- Sheet Content Area -->
            <!-- Sheet Content Area -->
            <div style="flex: 1; overflow-y: auto; -webkit-overflow-scrolling: touch; display: flex; flex-direction: column; background: #FFFFFF; position: relative;">
                
                <!-- Loading State View -->
                <div id="complete-payment-loading" style="display: flex; flex: 1; flex-direction: column; align-items: center; justify-content: center; gap: 16px; padding: 40px 24px;">
                    <div style="width: 42px; height: 42px; border: 3.5px solid rgba(255, 87, 34, 0.2); border-top-color: #FF5722; border-radius: 50%; animation: inAppSpin 0.75s linear infinite;"></div>
                    <div style="font-size: 15px; font-weight: 700; color: #1E293B;">Connecting to Paystack...</div>
                    <div style="font-size: 12.5px; color: #64748B;">Please wait while the in-app payment checkout loads.</div>
                </div>

                <!-- Error View -->
                <div id="complete-payment-error" style="display: none; flex: 1; flex-direction: column; align-items: center; justify-content: center; gap: 14px; padding: 40px 24px; text-align: center;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div id="complete-payment-error-text" style="font-size: 14px; font-weight: 700; color: #1E293B; line-height: 1.4;"></div>
                    <button type="button" onclick="startCompletePaymentFlow()" style="padding: 10px 22px; background: #FF5722; color: #FFFFFF; border: none; border-radius: 12px; font-size: 13.5px; font-weight: 700; cursor: pointer;">Try Again</button>
                </div>

                <!-- In-App Payment Browser Frame Container (Loads Paystack Checkout Seamlessly Inside Sheet) -->
                <div id="complete-payment-iframe-container" style="display: none; flex: 1; width: 100%; height: 100%; min-height: 540px; position: relative; background: #FFFFFF;">
                    <iframe id="complete-payment-iframe" src="about:blank" style="width: 100%; height: 100%; min-height: 540px; border: none; display: block;" allow="payment *; camera *; microphone *; clipboard-read *; clipboard-write *"></iframe>
                </div>

            </div>
        </div>
    </div>

    <style>
        @keyframes inAppSpin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        // IN-APP DEPOSIT & PAYMENT SYSTEM CONTROLLER â€” PAYSTACK ONLY
        // Zero external redirects. 100% inside Webview DOM. Never escapes to Safari.
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

        (function() {
            // Strip target="_blank" dynamically from any injected links
            document.addEventListener('click', function(e) {
                const anchor = e.target.closest('a[target="_blank"]');
                if (anchor) anchor.removeAttribute('target');
            }, true);
        })();

        let _inappSelectedGateway = 'paystack';
        let _inappActiveRef = null;
        let _inappActiveAmt = 2000;
        let _inappPollTimer = null;
        let _inappIsVerifying = false;
        let _inappIsInitializing = false;

        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        // PLATFORM DETECTION SUBSYSTEM
        // Distinguishes NativePHP iOS (SFSafariViewController), NativePHP Android
        // (Chrome Custom Tabs), and Desktop/Mobile Web (Paystack Inline JS SDK).
        // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
        const PlatformDetector = {
            getPlatform: function() {
                // 1. Injected Native Bridge interfaces inside WebView
                if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.nativephpBridge) {
                    return 'ios';
                }
                if (window._native || window.NativeAndroid || window.AndroidBridge) {
                    return 'android';
                }

                // 2. User-Agent signatures for NativePHP shells and mobile devices
                const ua = navigator.userAgent || '';
                if (/NativePHP-iOS|NativeiOS/i.test(ua)) {
                    return 'ios';
                }
                if (/NativePHP-Android|NativeAndroid/i.test(ua)) {
                    return 'android';
                }
                if (/Android/i.test(ua)) {
                    return 'android';
                }
                if (/iPhone|iPad|iPod/i.test(ua)) {
                    return 'ios';
                }

                // 3. Server-detected platform passed via Blade / middleware
                const serverPlatform = "<?php echo e($currentPlatform ?? 'web'); ?>";
                const serverIsNative = <?php echo e(!empty($isNativePlatform) ? 'true' : 'false'); ?>;
                if (serverIsNative && serverPlatform === 'ios') return 'ios';
                if (serverIsNative && serverPlatform === 'android') return 'android';

                return 'web';
            },
            isIOSNative: function() {
                const ua = navigator.userAgent || '';
                return (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.nativephpBridge)
                    || /NativePHP-iOS|NativeiOS/i.test(ua)
                    || (("<?php echo e($currentPlatform ?? 'web'); ?>" === 'ios') && <?php echo e(!empty($isNativePlatform) ? 'true' : 'false'); ?>);
            },
            isAndroidNative: function() {
                const ua = navigator.userAgent || '';
                return Boolean(window._native)
                    || Boolean(window.NativeAndroid)
                    || Boolean(window.AndroidBridge)
                    || /NativePHP-Android|NativeAndroid/i.test(ua)
                    || /Android/i.test(ua)
                    || ("<?php echo e($currentPlatform ?? 'web'); ?>" === 'android');
            },
            isDesktopWeb: function() {
                return !this.isIOSNative() && !this.isAndroidNative();
            }
        };

        function ensurePaystackInlineLoaded() {
            if (typeof window.PaystackPop !== 'undefined' && typeof window.PaystackPop.setup === 'function') {
                return Promise.resolve();
            }
            return new Promise(function(resolve, reject) {
                let checkCount = 0;
                const checkInterval = setInterval(function() {
                    checkCount++;
                    if (typeof window.PaystackPop !== 'undefined' && typeof window.PaystackPop.setup === 'function') {
                        clearInterval(checkInterval);
                        resolve();
                    } else if (checkCount > 40) {
                        clearInterval(checkInterval);
                        reject(new Error('Paystack checkout SDK could not be loaded. Check your internet connection.'));
                    }
                }, 100);

                const existingScript = document.querySelector('script[src*="paystack.co/v1/inline.js"]');
                if (!existingScript) {
                    const script = document.createElement('script');
                    script.src = 'https://js.paystack.co/v1/inline.js';
                    script.async = true;
                    script.onload = function() {
                        clearInterval(checkInterval);
                        resolve();
                    };
                    script.onerror = function() {
                        clearInterval(checkInterval);
                        reject(new Error('Failed to load Paystack inline script.'));
                    };
                    document.head.appendChild(script);
                }
            });
        }

        function openDepositModal(amt) {
            const depositAmt = (amt && Number(amt) > 0) ? Number(amt) : 2000;
            _inappActiveAmt = depositAmt;

            const input = document.getElementById('native-deposit-amount-input');
            if (input) input.value = depositAmt;
            updateInAppAmountLabel();

            const modal = document.getElementById('native-deposit-screen-modal');
            const sheet = document.getElementById('native-deposit-sheet');

            if (modal && sheet) {
                modal.style.display = 'flex';
                requestAnimationFrame(function() {
                    modal.style.opacity = '1';
                    sheet.style.transform = 'translateY(0)';
                });
            }

            if (window.navigator && typeof window.navigator.vibrate === 'function') {
                try { window.navigator.vibrate(20); } catch(e) {}
            }
        }

        function closeDepositModal() {
            const modal = document.getElementById('native-deposit-screen-modal');
            const sheet = document.getElementById('native-deposit-sheet');

            if (modal && sheet) {
                sheet.style.transform = 'translateY(100%)';
                modal.style.opacity = '0';
                setTimeout(function() {
                    modal.style.display = 'none';
                }, 260);
            }
        }

        function selectInAppGateway(gw) {
            _inappSelectedGateway = 'paystack';
        }

        function setInAppPreset(val, btnEl) {
            const input = document.getElementById('native-deposit-amount-input');
            if (input) input.value = val;
            document.querySelectorAll('.inapp-preset-btn').forEach(b => {
                b.style.borderColor = 'rgba(255,255,255,0.1)';
                b.style.background = 'rgba(30, 27, 75, 0.4)';
                b.style.color = 'rgba(255,255,255,0.85)';
            });
            if (btnEl) {
                btnEl.style.borderColor = '#7C3AED';
                btnEl.style.background = 'rgba(16, 185, 129, 0.16)';
                btnEl.style.color = '#7C3AED';
            }
            updateInAppAmountLabel();
        }

        function updateInAppAmountLabel() {
            const input = document.getElementById('native-deposit-amount-input');
            const val = input ? parseFloat(input.value) : 2000;
            const sym = "<?php echo e($inappCurrencySymbol); ?>";
            const btnLabel = document.getElementById('inapp-fund-wallet-btn-label');
            const pill = document.getElementById('complete-payment-amount-pill');

            const formatted = sym + (isNaN(val) || val <= 0 ? '0' : val.toLocaleString());
            if (btnLabel) btnLabel.textContent = 'Fund Wallet (' + formatted + ')';
            if (pill) pill.textContent = formatted;
            _inappActiveAmt = isNaN(val) ? 2000 : val;
        }

        function closeCompletePaymentSheet() {
            const modal = document.getElementById('native-complete-payment-modal');
            const sheet = document.getElementById('native-complete-payment-sheet');
            const iframe = document.getElementById('complete-payment-iframe');
            const iframeContainer = document.getElementById('complete-payment-iframe-container');

            // Clean up iframe check interval
            if (window._iframeCheckInterval) {
                clearInterval(window._iframeCheckInterval);
                window._iframeCheckInterval = null;
            }

            if (iframe) iframe.src = 'about:blank';
            if (iframeContainer) iframeContainer.style.display = 'none';

            if (modal && sheet) {
                sheet.style.transform = 'translateY(100%)';
                modal.style.opacity = '0';
                setTimeout(function() {
                    modal.style.display = 'none';
                }, 280);
            }
            stopInAppPolling();
        }

        async function startCompletePaymentFlow() {
            if (_inappIsInitializing) {
                console.log('[InAppPayment] Already initializing, ignoring duplicate call');
                return;
            }
            _inappIsInitializing = true;

            const input = document.getElementById('native-deposit-amount-input');
            const amt = input ? parseFloat(input.value) : 2000;
            const err = document.getElementById('inapp-amount-error');

            if (!amt || amt < 100) {
                _inappIsInitializing = false;
                if (err) {
                    err.textContent = 'Minimum deposit amount is <?php echo e($inappCurrencySymbol); ?>100.';
                    err.style.display = 'block';
                }
                return;
            }
            if (err) err.style.display = 'none';

            // Show a brief loading state in the deposit sheet button
            const fundBtn = document.getElementById('inapp-fund-wallet-btn');
            const fundBtnLabel = document.getElementById('inapp-fund-wallet-btn-label');
            const origBtnText = fundBtnLabel ? fundBtnLabel.textContent : '';
            if (fundBtn) { fundBtn.style.pointerEvents = 'none'; fundBtn.style.opacity = '0.75'; }
            if (fundBtnLabel) fundBtnLabel.innerHTML = '<span style="display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:inAppSpin 0.65s linear infinite;vertical-align:middle;margin-right:8px;"></span>Connecting...';

            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            // BRANCH 1: DESKTOP / WEB BROWSER
            // Uses Paystack v1 inline popup JS SDK (PaystackPop.setup).
            // Zero external redirect; keeps the user 100% inside page overlay.
            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            if (PlatformDetector.isDesktopWeb()) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const res = await fetch("<?php echo e(url('/transaction/initialize')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            amount: amt,
                            currency: "<?php echo e($inappCurrencyCode); ?>",
                            gateway: 'paystack',
                            email: "<?php echo e(auth()->user()?->email); ?>",
                            open_in_app: false
                        })
                    });

                    const data = await res.json();
                    if (!res.ok || !data.status) {
                        throw new Error(data.message || 'Unable to initialize checkout.');
                    }

                    const ref = data.data?.reference;
                    const publicKey = data.data?.public_key || "<?php echo e(config('services.paystack.public_key')); ?>";
                    const userEmail = "<?php echo e(auth()->user()?->email); ?>";
                    _inappActiveRef = ref;

                    try {
                        localStorage.setItem('pending_deposit_ref', ref);
                        localStorage.setItem('pending_deposit_amt', String(amt));
                        localStorage.setItem('pending_deposit_time', String(Date.now()));
                    } catch(e) {}

                    await ensurePaystackInlineLoaded();

                    const handler = PaystackPop.setup({
                        key: publicKey,
                        email: userEmail,
                        amount: Math.round(amt * 100),
                        currency: "<?php echo e($inappCurrencyCode); ?>",
                        ref: ref,
                        callback: function(response) {
                            console.log('[Paystack Desktop Web] Payment callback received:', response);
                            const verifiedRef = response.reference || response.trxref || ref;
                            checkInAppVerification(verifiedRef, amt);
                        },
                        onClose: function() {
                            console.log('[Paystack Desktop Web] Popup closed by user');
                            checkInAppVerification(ref, amt);
                        }
                    });

                    handler.openIframe();

                } catch (e) {
                    console.error('[Web Deposit Flow Error]', e);
                    if (err) {
                        err.textContent = e.message || 'An error occurred initializing payment.';
                        err.style.display = 'block';
                    }
                } finally {
                    setTimeout(function() {
                        _inappIsInitializing = false;
                    }, 1500);
                    if (fundBtn) { fundBtn.style.pointerEvents = 'auto'; fundBtn.style.opacity = '1'; }
                    if (fundBtnLabel) fundBtnLabel.textContent = origBtnText;
                }
                return;
            }

            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            // BRANCH 2: ANDROID NATIVE SHELL
            // Opens Paystack in-app modal sheet (PaystackPop.setup).
            // Matches iOS SFSafariViewController: stays inside app, never kicks to dashboard.
            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            if (PlatformDetector.isAndroidNative()) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const res = await fetch("<?php echo e(url('/transaction/initialize')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            amount: amt,
                            currency: "<?php echo e($inappCurrencyCode); ?>",
                            gateway: 'paystack',
                            email: "<?php echo e(auth()->user()?->email); ?>",
                            open_in_app: false
                        })
                    });

                    const data = await res.json();
                    if (!res.ok || !data.status) {
                        throw new Error(data.message || 'Unable to initialize checkout.');
                    }

                    const ref = data.data?.reference;
                    const publicKey = data.data?.public_key || "<?php echo e(config('services.paystack.public_key')); ?>";
                    const userEmail = "<?php echo e(auth()->user()?->email); ?>";
                    _inappActiveRef = ref;

                    try {
                        localStorage.setItem('pending_deposit_ref', ref);
                        localStorage.setItem('pending_deposit_amt', String(amt));
                        localStorage.setItem('pending_deposit_time', String(Date.now()));
                    } catch(e) {}

                    // Ensure Paystack inline script is ready
                    await ensurePaystackInlineLoaded();

                    // Start background polling immediately as safety net (overlaying deposit page)
                    startInAppPolling(ref, amt);

                    // Launch Paystack in-app modal sheet directly on top of deposit page
                    const handler = PaystackPop.setup({
                        key: publicKey,
                        email: userEmail,
                        amount: Math.round(amt * 100),
                        currency: "<?php echo e($inappCurrencyCode); ?>",
                        ref: ref,
                        callback: function(response) {
                            console.log('[Android In-App Payment] Payment callback:', response);
                            const verifiedRef = response.reference || response.trxref || ref;
                            checkInAppVerification(verifiedRef, amt);
                        },
                        onClose: function() {
                            console.log('[Android In-App Payment] Checkout closed by user');
                            checkInAppVerification(ref, amt);
                        }
                    });

                    handler.openIframe();

                } catch (e) {
                    console.error('[Android Deposit Flow Error]', e);
                    if (err) {
                        err.textContent = e.message || 'An error occurred initializing payment.';
                        err.style.display = 'block';
                    }
                } finally {
                    setTimeout(function() {
                        _inappIsInitializing = false;
                    }, 1500);
                    if (fundBtn) { fundBtn.style.pointerEvents = 'auto'; fundBtn.style.opacity = '1'; }
                    if (fundBtnLabel) fundBtnLabel.textContent = origBtnText;
                }
                return;
            }

            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            // BRANCH 3: iOS NATIVE SHELL (ORIGINAL CODE PATH â€” 100% UNTOUCHED)
            // Uses SFSafariViewController via NativePHP Browser::inApp()
            // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch("<?php echo e(url('/transaction/initialize')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        amount: amt,
                        currency: "<?php echo e($inappCurrencyCode); ?>",
                        gateway: 'paystack',
                        email: "<?php echo e(auth()->user()?->email); ?>",
                        open_in_app: true
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.status) {
                    throw new Error(data.message || 'Unable to initialize checkout.');
                }

                const ref = data.data?.reference;
                const authUrl = data.data?.authorization_url;
                _inappActiveRef = ref;

                // Store pending payment session for recovery
                try {
                    localStorage.setItem('pending_deposit_ref', ref);
                    localStorage.setItem('pending_deposit_amt', String(amt));
                    localStorage.setItem('pending_deposit_time', String(Date.now()));
                } catch(e) {}

                // Close deposit amount sheet so native in-app browser sheet slides up cleanly
                closeDepositModal();

                // Start real-time background deposit-status poller immediately (up to ~60s)
                startDepositStatusPoller(ref, amt);

            } catch (e) {
                console.error('[Deposit Flow Error]', e);
                // Show error in the deposit sheet if still visible
                if (err) {
                    err.textContent = e.message || 'An error occurred initializing payment.';
                    err.style.display = 'block';
                }
            } finally {
                // Restore button state and debounce lock
                setTimeout(function() {
                    _inappIsInitializing = false;
                }, 1500);
                if (fundBtn) { fundBtn.style.pointerEvents = 'auto'; fundBtn.style.opacity = '1'; }
                if (fundBtnLabel) fundBtnLabel.textContent = origBtnText;
            }
        }

        // ====================================================
        // Universal In-Place Balance Updater (Zero-Flicker, No Reload)
        // ====================================================
        function updateBalanceInPlace(newBalance, currencySymbol = null) {
            if (newBalance === undefined || newBalance === null || isNaN(Number(newBalance))) return;
            const num = Number(newBalance);
            const formatted = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const sym = currencySymbol || (document.getElementById('dash-wallet-symbol')?.textContent || '₦');

            const ids = [
                'user-wallet-balance',
                'dash-wallet-balance',
                'wallet-tab-balance',
                'settings-wallet-balance',
                'native-wallet-balance-num'
            ];
            ids.forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.textContent = formatted;
            });

            const statsBal = document.getElementById('stats-balance');
            if (statsBal) {
                statsBal.textContent = sym + num.toLocaleString('en-US', { maximumFractionDigits: 0 });
            }

            if (currencySymbol) {
                ['user-wallet-symbol', 'dash-wallet-symbol', 'wallet-tab-symbol'].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) el.textContent = currencySymbol;
                });
            }

            try {
                localStorage.setItem('user_wallet_balance', String(num));
            } catch(e) {}

            window.dispatchEvent(new CustomEvent('balance-updated', { detail: { balance: num } }));
        }
        window.updateBalanceInPlace = updateBalanceInPlace;

        // Single-flight balance fetcher
        let _balanceFetchInFlight = false;
        async function fetchAndRefreshBalance() {
            if (_balanceFetchInFlight) return;
            _balanceFetchInFlight = true;

            try {
                const res = await fetch("<?php echo e(url('/wallet/balance')); ?>", {
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.status && data.balance !== undefined) {
                        updateBalanceInPlace(data.balance, data.symbol);
                    }
                }
            } catch (e) {
                console.debug('[BalanceSync] Fetch error:', e);
            } finally {
                _balanceFetchInFlight = false;
            }
        }
        window.fetchAndRefreshBalance = fetchAndRefreshBalance;

        // ====================================================
        // Deposit Status Poller (Single-flight guard, up to ~60s)
        // ====================================================
        let _depositStatusPollTimer = null;
        let _depositStatusInFlight = false;
        let _depositStatusPollCount = 0;
        const MAX_DEPOSIT_POLLS = 25; // 25 * 2.5s = ~62.5s

        function startDepositStatusPoller(reference, expectedAmount) {
            stopDepositStatusPoller();
            _depositStatusPollCount = 0;

            _depositStatusPollTimer = setInterval(async function() {
                _depositStatusPollCount++;
                if (_depositStatusPollCount > MAX_DEPOSIT_POLLS) {
                    console.log('[DepositPoll] Max poll limit reached (~60s). Stopping poller.');
                    stopDepositStatusPoller();
                    return;
                }

                if (_depositStatusInFlight) return;
                _depositStatusInFlight = true;

                try {
                    const res = await fetch("<?php echo e(url('/wallet/deposit-status')); ?>/" + encodeURIComponent(reference), {
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        },
                        credentials: 'same-origin'
                    });

                    if (!res.ok) return;

                    const data = await res.json();

                    if (data.status && (data.verified || data.deposit_status === 'success')) {
                        console.log('[DepositPoll] Payment verified successfully via deposit-status poll:', data);
                        stopDepositStatusPoller();

                        try {
                            localStorage.removeItem('pending_deposit_ref');
                            localStorage.removeItem('pending_deposit_amt');
                            localStorage.removeItem('pending_deposit_time');
                        } catch(e) {}

                        // Update balance in place with fetch (no full reload, no flicker)
                        if (data.wallet_balance !== undefined) {
                            updateBalanceInPlace(data.wallet_balance);
                        } else {
                            await fetchAndRefreshBalance();
                        }

                        // Instant celebratory feedback without full reload
                        if (typeof window.showPaymentSplash === 'function') {
                            window.showPaymentSplash("<?php echo e($inappCurrencyCode); ?>", expectedAmount || data.amount);
                        }
                    } else if (data.canceled || data.deposit_status === 'cancelled' || data.deposit_status === 'failed') {
                        console.log('[DepositPoll] Deposit failed or cancelled.');
                        stopDepositStatusPoller();
                    }
                } catch (e) {
                    console.debug('[DepositPoll] Poll error:', e);
                } finally {
                    _depositStatusInFlight = false;
                }
            }, 2500);
        }

        function stopDepositStatusPoller() {
            if (_depositStatusPollTimer) {
                clearInterval(_depositStatusPollTimer);
                _depositStatusPollTimer = null;
            }
        }

        async function pollDepositStatusOnce(reference) {
            if (!reference || _depositStatusInFlight) return;
            _depositStatusInFlight = true;
            try {
                const res = await fetch("<?php echo e(url('/wallet/deposit-status')); ?>/" + encodeURIComponent(reference), {
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    },
                    credentials: 'same-origin'
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.status && (data.verified || data.deposit_status === 'success')) {
                        stopDepositStatusPoller();
                        try {
                            localStorage.removeItem('pending_deposit_ref');
                            localStorage.removeItem('pending_deposit_amt');
                            localStorage.removeItem('pending_deposit_time');
                        } catch(e) {}
                        if (data.wallet_balance !== undefined) {
                            updateBalanceInPlace(data.wallet_balance);
                        } else {
                            await fetchAndRefreshBalance();
                        }
                    }
                }
            } catch(e) {
            } finally {
                _depositStatusInFlight = false;
            }
        }

        // ====================================================
        // Lifecycle & Browser Dismiss Hook Listeners
        // ====================================================
        // (i) NativePHP Browser close/dismiss events
        window.addEventListener('nativephp:browser:closed', function() {
            console.log('[InAppBrowser] Browser closed event caught -> refreshing balance');
            fetchAndRefreshBalance();
            const pendingRef = localStorage.getItem('pending_deposit_ref');
            if (pendingRef) pollDepositStatusOnce(pendingRef);
        });
        window.addEventListener('native:browser:dismissed', function() {
            console.log('[InAppBrowser] Browser dismissed event caught -> refreshing balance');
            fetchAndRefreshBalance();
            const pendingRef = localStorage.getItem('pending_deposit_ref');
            if (pendingRef) pollDepositStatusOnce(pendingRef);
        });

        // Cross-window / storage event
        window.addEventListener('storage', function(e) {
            if (e.key === 'last_payment_event') {
                try {
                    const ev = JSON.parse(e.newValue);
                    if (ev && ev.type === 'PAYMENT_CALLBACK_SUCCESS') {
                        fetchAndRefreshBalance();
                        stopDepositStatusPoller();
                        try {
                            localStorage.removeItem('pending_deposit_ref');
                            localStorage.removeItem('pending_deposit_amt');
                            localStorage.removeItem('pending_deposit_time');
                        } catch(err) {}
                    }
                } catch(err) {}
            }
        });

        // (ii) visibilitychange: when user returns from in-app browser or background
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                console.log('[Lifecycle] Page visibility restored -> refreshing balance');
                fetchAndRefreshBalance();
                const pendingRef = localStorage.getItem('pending_deposit_ref');
                if (pendingRef) pollDepositStatusOnce(pendingRef);
            }
        });

        // [BFCACHE-DEFENSE] pageshow: handles bfcache restore (event.persisted) and window activation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                console.log('[BFCACHE-DEFENSE] Restored from bfcache (event.persisted)');
            }
            fetchAndRefreshBalance();
            const pendingRef = localStorage.getItem('pending_deposit_ref');
            if (pendingRef) pollDepositStatusOnce(pendingRef);
        });

        // [BFCACHE-DEFENSE] pagehide: cleans up active polling before entering bfcache
        window.addEventListener('pagehide', function(event) {
            stopDepositStatusPoller();
        });

        // window focus
        window.addEventListener('focus', function() {
            fetchAndRefreshBalance();
        });

        // Global exports
        window.openDepositModal = openDepositModal;
        window.closeDepositModal = closeDepositModal;
        window.startCompletePaymentFlow = startCompletePaymentFlow;
        window.closeCompletePaymentSheet = closeCompletePaymentSheet;
        window.selectDepositPreset = setInAppPreset;
        window.processNativeDeposit = openDepositModal;
    </script>

    <!-- Instant Splash Payment Success Modal Overlay -->
    <div id="payment-splash-overlay" style="display: none; position: fixed; inset: 0; z-index: 999999; background: rgba(30, 27, 75, 0.88); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); align-items: center; justify-content: center; padding: 24px;">
        <div style="background: #FFFFFF; border-radius: 28px; padding: 36px 24px; max-width: 320px; width: 100%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,0.35); border: 2px solid rgba(124, 58, 237, 0.35); animation: splashPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #7C3AED, #7C3AED); display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; box-shadow: 0 12px 28px rgba(124, 58, 237, 0.45); color: #FFFFFF;">
                <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div style="font-size: 21px; font-weight: 900; color: #1E1B4B; margin-bottom: 6px; letter-spacing: -0.02em;">Payment Splash!</div>
            <div style="font-size: 13px; font-weight: 600; color: #6B7A99; margin-bottom: 18px;">Wallet Credited Instantly</div>
            <div id="splash-amount-display" style="display: inline-block; background: #EEF1F8; border: 1.5px solid rgba(124, 58, 237, 0.3); border-radius: 16px; padding: 10px 22px; font-size: 22px; font-weight: 900; color: #7C3AED; font-family: 'JetBrains Mono', monospace; letter-spacing: -0.01em;">
                +â‚¦2,000.00
            </div>
        </div>
    </div>

    <?php if(session('just_logged_in')): ?>
    <script>
        // Transfer login flag from server session to client sessionStorage
        // This flag is used to suppress auto-recovery immediately after login
        console.log('[LOGIN-SUPPRESSION] Server session flag detected:', <?php echo e(session('just_logged_in')); ?>);
        try {
            const timestamp = '<?php echo e(session('just_logged_in') * 1000); ?>';
            sessionStorage.setItem('segyric_just_logged_in', timestamp);
            sessionStorage.setItem('verixa_just_logged_in', timestamp);
            sessionStorage.setItem('Verixa_just_logged_in', timestamp);
            console.log('[LOGIN-SUPPRESSION] sessionStorage flag set:', timestamp);
        } catch(e) {
            console.debug('[LOGIN-SUPPRESSION] SessionStorage not available:', e.message);
        }
    </script>
    <?php endif; ?>

    <script>
        try {
            const now = String(Date.now());
            sessionStorage.setItem('segyric_page_load_time', now);
            sessionStorage.setItem('verixa_page_load_time', now);
            sessionStorage.setItem('Verixa_page_load_time', now);
        } catch(e) {}
    </script>

    <!-- Global Real-Time Payment Synchronizer & Background Poller -->
    <script src="<?php echo e(url('js/payment-sync.js', [], false)); ?>"></script>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/native/layouts/mobile.blade.php ENDPATH**/ ?>
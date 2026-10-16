@php
    $user = auth()->user();
    $wallet = $user ? ($user->wallet ?? app(\App\Application\Services\WalletService::class)->getOrCreateUserWallet($user)) : null;
    $currencySymbol = $wallet?->currency?->symbol ?? '₦';
    $currencyCode = $wallet?->currency?->code ?? 'NGN';
    $stripeKey = (string) config('services.stripe.public_key', env('STRIPE_PUBLIC_KEY', ''));
    $checkoutUrl = $deposit?->gateway_response['url'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1E1B4B">
    <title>Checkout • Verixa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body {
            width: 100%; height: 100%; min-height: 100vh;
            background: linear-gradient(180deg, #07101F 0%, #0B192E 50%, #1E1B4B 100%);
            color: #fff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex; flex-direction: column;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }
        .checkout-header {
            height: 56px; padding: 0 16px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(7, 16, 31, 0.9); backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            flex-shrink: 0; z-index: 10;
        }
        .checkout-back-btn {
            color: #FFFFFF; text-decoration: none; display: flex; align-items: center;
            justify-content: center; width: 38px; height: 38px; border-radius: 50%;
            background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .checkout-title {
            font-size: 17px; font-weight: 800; color: #FFFFFF;
        }
        .checkout-amount-badge {
            font-size: 13px; font-weight: 800; color: #7C3AED;
            font-family: 'JetBrains Mono', monospace; background: rgba(16, 185, 129, 0.15);
            padding: 4px 10px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .checkout-frame-container {
            flex: 1; width: 100%; height: 100%; position: relative; background: #FFFFFF;
        }
        .checkout-iframe {
            width: 100%; height: 100%; border: none; display: block;
        }
        .loading-screen {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center; background: #07101F; z-index: 5;
            gap: 16px;
        }
        .checkout-spinner {
            width: 44px; height: 44px;
            border: 3.5px solid rgba(16, 185, 129, 0.2);
            border-top-color: #7C3AED;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        /* Success overlay */
        .success-overlay {
            display: none; position: fixed; inset: 0; z-index: 99999;
            background: rgba(7, 16, 31, 0.95); backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            flex-direction: column; align-items: center; justify-content: center;
        }
        .success-icon {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED, #059669);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; box-shadow: 0 12px 28px rgba(16, 185, 129, 0.45);
            animation: successPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes successPop { 0% { transform: scale(0); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <header class="checkout-header">
        <a href="{{ route('mobile.wallet', [], false) }}" class="checkout-back-btn" aria-label="Back">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </a>
        <span class="checkout-title">Stripe Checkout</span>
        <div class="checkout-amount-badge">
            {{ $currencySymbol }}{{ number_format($amount ?? 0) }}
        </div>
    </header>

    <div class="checkout-frame-container">
        <div id="loading-screen" class="loading-screen">
            <div class="checkout-spinner"></div>
            <div style="font-size: 15px; font-weight: 700; color: #FFFFFF;">Loading Secure Checkout...</div>
            <div style="font-size: 12px; color: rgba(255,255,255,0.5);">Connecting to Stripe Payment Gateway</div>
        </div>

        @if(!empty($checkoutUrl))
            <iframe 
                id="checkout-iframe" 
                class="checkout-iframe" 
                src="{{ $checkoutUrl }}" 
                allow="payment *; camera *; microphone *; clipboard-read *; clipboard-write *"
                sandbox="allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts allow-top-navigation-by-user-activation"
                onload="document.getElementById('loading-screen').style.display='none';"
            ></iframe>
        @else
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 14px; padding: 24px; text-align: center; color: #1E293B;">
                <div style="font-size: 16px; font-weight: 800;">Checkout session not found</div>
                <a href="{{ route('mobile.wallet', [], false) }}" style="padding: 10px 20px; background: #7C3AED; color: #fff; border-radius: 12px; text-decoration: none; font-weight: 700;">Return to Wallet</a>
            </div>
        @endif
    </div>

    <!-- Success Overlay -->
    <div class="success-overlay" id="success-overlay">
        <div class="success-icon">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div style="font-size: 22px; font-weight: 900; color: #fff; margin-bottom: 6px;">Payment Successful!</div>
        <div style="font-size: 14px; color: rgba(255,255,255,0.6); margin-bottom: 16px;">Your wallet has been credited</div>
        <div id="success-amount" style="font-size: 28px; font-weight: 900; color: #7C3AED; font-family: 'JetBrains Mono', monospace;">
            +{{ $currencySymbol }}{{ number_format($amount ?? 0) }}
        </div>
    </div>

    <script>
        const REFERENCE = "{{ $reference ?? '' }}";
        const WALLET_URL = "{{ route('mobile.wallet', [], false) }}";

        let pollInterval = setInterval(async function() {
            if (!REFERENCE) return;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch("{{ url('/wallet/verify', [], false) }}/" + encodeURIComponent(REFERENCE), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();
                if (data.status && data.verified) {
                    clearInterval(pollInterval);
                    document.getElementById('success-overlay').style.display = 'flex';
                    setTimeout(() => {
                        window.location.replace(WALLET_URL);
                    }, 2000);
                }
            } catch(e) {}
        }, 2500);
    </script>
</body>
</html>

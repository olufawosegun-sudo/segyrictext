<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $isCancelled = !empty($isCanceled);
        $isVerified = !$isCancelled && (($verificationResult['verified'] ?? false) || ($deposit && $deposit->isSuccess()));
        $displayAmount = (float) ($verificationResult['amount'] ?? ($deposit->amount ?? 0));
        $currencyCode = strtoupper($deposit->currency ?? 'NGN');
        $currencySymbol = ($currencyCode === 'USD') ? '$' : '₦';
    @endphp
    <title>{{ $isCancelled ? 'Payment Cancelled' : ($isVerified ? 'Payment Received' : 'Payment Status') }} — Simora</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #0B132B;
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            color: #FFFFFF;
            padding: 20px;
        }
        .card {
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 32px 24px;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .icon-circle.loading {
            background: rgba(16, 185, 129, 0.1);
            border: 3px solid rgba(16, 185, 129, 0.2);
            border-top-color: #7C3AED;
            animation: spin 0.8s linear infinite;
        }
        .icon-circle.success {
            background: linear-gradient(135deg, #7C3AED, #059669);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .icon-circle.canceled {
            background: linear-gradient(135deg, #475569, #334155);
            box-shadow: 0 8px 24px rgba(71, 85, 105, 0.4);
            animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .icon-circle.error {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
            animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .subtitle {
            font-size: 13.5px;
            color: #94A3B8;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .amount-badge {
            display: inline-block;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            border-radius: 12px;
            padding: 8px 18px;
            font-size: 20px;
            font-weight: 900;
            color: #7C3AED;
            font-family: 'SF Mono', Monaco, monospace;
            margin-bottom: 24px;
        }
        .btn-action {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #7C3AED, #059669);
            color: #FFFFFF;
            border: none;
            border-radius: 14px;
            padding: 13px 20px;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            transition: transform 0.15s ease;
        }
        .btn-action.btn-canceled {
            background: linear-gradient(135deg, #334155, #1E293B);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }
        .btn-action:active {
            transform: scale(0.97);
        }
        .hint {
            font-size: 12px;
            color: #94A3B8;
            margin-top: 14px;
            font-weight: 500;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes popIn {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="card">
        @if($isCancelled)
            <div id="status-icon" class="icon-circle canceled">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
            <h2 id="status-title" class="title">Payment Cancelled</h2>
            <p id="status-desc" class="subtitle">This transaction was cancelled. No funds were deducted.</p>

            <button id="action-btn" class="btn-action btn-canceled" onclick="closeInAppWindow()">
                Tap Done to Return
            </button>
            <p id="status-hint" class="hint">You can tap Done in the top corner to return to the app.</p>

        @elseif($isVerified)
            <div id="status-icon" class="icon-circle success">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h2 id="status-title" class="title">Payment Received!</h2>
            <p id="status-desc" class="subtitle">Your deposit has been confirmed and credited to your wallet.</p>

            <div id="status-amount" class="amount-badge">
                +{{ $currencySymbol }}{{ number_format($displayAmount, 2) }}
            </div>

            <button id="action-btn" class="btn-action" onclick="closeInAppWindow()">
                Tap Done to Return
            </button>
            <p id="status-hint" class="hint">Tap <strong>Done</strong> in the top corner to return to the app.</p>
        @else
            <div id="status-icon" class="icon-circle loading"></div>
            <h2 id="status-title" class="title">Verifying Payment...</h2>
            <p id="status-desc" class="subtitle">Please wait a moment while we confirm your deposit.</p>
            
            <div id="status-amount" class="amount-badge" style="display: {{ $displayAmount > 0 ? 'inline-block' : 'none' }};">
                +{{ $currencySymbol }}{{ number_format($displayAmount, 2) }}
            </div>

            <button id="action-btn" class="btn-action" style="display: none;" onclick="closeInAppWindow()">
                Tap Done to Return
            </button>
            <p id="status-hint" class="hint">Confirming transaction with provider...</p>
        @endif
    </div>

    <script>
        (function() {
            const reference = "{{ $reference }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const isCancelled = {{ $isCancelled ? 'true' : 'false' }};
            const isAlreadyVerified = {{ $isVerified ? 'true' : 'false' }};
            const currencySymbol = "{{ $currencySymbol }}";

            // Universal close handler
            window.closeInAppWindow = function() {
                broadcastEvent(isCancelled ? 'PAYMENT_CANCELLED' : 'PAYMENT_CALLBACK_SUCCESS');
                try {
                    if (window.webkit?.messageHandlers?.nativeApp) {
                        window.webkit.messageHandlers.nativeApp.postMessage({ action: 'close_in_app_browser', reference: reference });
                    }
                } catch(e) {}
                try {
                    window.close();
                } catch(e) {}
            };

            // Notify opener/parent/native app immediately
            function broadcastEvent(type) {
                const payload = { type: type, reference: reference, timestamp: Date.now() };
                try {
                    if (window.opener && window.opener !== window) {
                        window.opener.postMessage(payload, '*');
                    }
                } catch(e) {}
                try {
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage(payload, '*');
                    }
                } catch(e) {}
                try {
                    if (window.webkit?.messageHandlers?.nativeApp) {
                        window.webkit.messageHandlers.nativeApp.postMessage({ action: 'close_in_app_browser', reference: reference, type: type });
                    }
                } catch(e) {}
                try {
                    localStorage.setItem('last_payment_event', JSON.stringify(payload));
                } catch(e) {}
            }

            if (isCancelled) {
                broadcastEvent('PAYMENT_CANCELLED');
                return;
            }

            if (isAlreadyVerified) {
                broadcastEvent('PAYMENT_CALLBACK_SUCCESS');
                return;
            }

            // If not verified server-side yet, verify via deposit-status endpoint
            async function verifyDeposit() {
                if (!reference) return;

                try {
                    const response = await fetch("{{ url('/wallet/deposit-status') }}/" + encodeURIComponent(reference), {
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'no-cache'
                        }
                    });

                    const data = await response.json();

                    if (data.status && (data.verified || data.deposit_status === 'success')) {
                        showSuccess(data.amount || "{{ $displayAmount }}");
                        broadcastEvent('PAYMENT_CALLBACK_SUCCESS');
                    } else if (data.canceled || data.deposit_status === 'cancelled') {
                        showCancelled();
                        broadcastEvent('PAYMENT_CANCELLED');
                    }
                } catch(e) {
                    console.debug('Callback verification check:', e);
                }
            }

            function showSuccess(amount) {
                const icon = document.getElementById('status-icon');
                const title = document.getElementById('status-title');
                const desc = document.getElementById('status-desc');
                const amtEl = document.getElementById('status-amount');
                const btn = document.getElementById('action-btn');
                const hint = document.getElementById('status-hint');

                if (icon) {
                    icon.className = 'icon-circle success';
                    icon.innerHTML = '<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                }
                if (title) title.textContent = 'Payment Received!';
                if (desc) desc.textContent = 'Your deposit has been confirmed and credited to your wallet.';

                if (amtEl && amount) {
                    amtEl.textContent = '+' + currencySymbol + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
                    amtEl.style.display = 'inline-block';
                }

                if (btn) {
                    btn.className = 'btn-action';
                    btn.textContent = 'Tap Done to Return';
                    btn.style.display = 'block';
                }
                if (hint) hint.innerHTML = 'Tap <strong>Done</strong> in the top corner to return to the app.';
            }

            function showCancelled() {
                const icon = document.getElementById('status-icon');
                const title = document.getElementById('status-title');
                const desc = document.getElementById('status-desc');
                const btn = document.getElementById('action-btn');
                const hint = document.getElementById('status-hint');

                if (icon) {
                    icon.className = 'icon-circle canceled';
                    icon.innerHTML = '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                }
                if (title) title.textContent = 'Payment Cancelled';
                if (desc) desc.textContent = 'This transaction was cancelled. No funds were deducted.';

                if (btn) {
                    btn.className = 'btn-action btn-canceled';
                    btn.textContent = 'Tap Done to Return';
                    btn.style.display = 'block';
                }
                if (hint) hint.textContent = 'You can tap Done in the top corner to return to the app.';
            }

            verifyDeposit();
        })();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password | Verixa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --navy-950: #030712;
            --navy-900: #050b18;
            --navy-850: #081326;
            --navy-800: #0d1e38;
            --teal-700: #0f766e;
            --teal-600: #0d9488;
            --teal-500: #14b8a6;
            --teal-400: #2dd4bf;
            --teal-300: #5eead4;
            --teal-glow: rgba(20, 184, 166, 0.45);
            --electric: #06b6d4;
            --cyan-400: #22d3ee;
            --emerald-400: #7C3AED;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
            --safe-top: env(safe-area-inset-top, 24px);
            --safe-bottom: env(safe-area-inset-bottom, 24px);
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            background: var(--navy-950);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
        }

        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 80% 50% at 20% -5%, rgba(20, 184, 166, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(6, 182, 212, 0.12) 0%, transparent 60%);
        }

        .dot-grid {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
            background: rgba(8, 19, 38, 0.95);
            border: 1px solid rgba(20, 184, 166, 0.18);
            border-radius: 24px;
            padding: 36px 28px calc(var(--safe-bottom) + 28px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.04) inset;
            backdrop-filter: blur(20px);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 14px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.12em;
            color: #fff;
            margin-bottom: 4px;
        }

        .service-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--teal-400);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: rgba(20, 184, 166, 0.1);
            border: 1px solid rgba(20, 184, 166, 0.25);
            border-radius: 30px;
            padding: 4px 10px;
            margin-bottom: 10px;
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal-400);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .brand-subtitle {
            font-size: 13px;
            color: var(--text-tertiary);
            margin-top: 4px;
        }

        .icon-lock {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: rgba(20, 184, 166, 0.12);
            border: 1px solid rgba(20, 184, 166, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            color: var(--text-primary);
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            -webkit-appearance: none;
        }

        .form-input:focus {
            border-color: rgba(20, 184, 166, 0.6);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.12);
            background: rgba(20, 184, 166, 0.05);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 24px rgba(20, 184, 166, 0.35);
            margin-top: 6px;
        }

        .btn-submit:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); filter: brightness(0.95); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .alert-error {
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            font-size: 13px;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .alert-success {
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--emerald-400);
            font-size: 13px;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            font-size: 13px;
            color: var(--text-tertiary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover { color: var(--teal-400); }
        .back-link a { color: var(--teal-300); font-weight: 700; text-decoration: none; }

        .hint-text {
            font-size: 13px;
            color: var(--text-tertiary);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="ambient-bg" aria-hidden="true"></div>
    <div class="dot-grid" aria-hidden="true"></div>

    <div class="auth-card">
        <div class="brand-header">
            <div class="brand-logo-container">
                <x-brand-logo size="72" :glow="true" :badge="false" />
            </div>
            <h1 class="brand-title">Verixa</h1>
            <div class="service-badge">
                <span class="pulse-dot"></span>
                Account Recovery
            </div>
        </div>

        <div class="icon-lock" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2dd4bf" stroke-width="2">
                <rect width="18" height="11" x="3" y="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>

        <p class="hint-text">Enter your email address and we'll send you a secure link to reset your password.</p>

        @if(session('success'))
            <div class="alert-success" id="success-msg">
                <strong>✓ Email Sent!</strong><br>
                {{ session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert-error">
                <strong>⚠️ Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        @if(!session('success'))
        <form id="forgotForm" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label class="form-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="form-input"
                    placeholder="you@example.com"
                >
            </div>

            <button type="submit" id="btn-submit" class="btn-submit">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.81.36 1.6.65 2.36a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.76.29 1.55.52 2.36.65A2 2 0 0 1 22 16.92z"/>
                </svg>
                Send Reset Link
            </button>
        </form>
        @endif

        <div class="back-link">
            ← Back to <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerHTML = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Sending...';
        });

        // Auto-reload on iOS Safari / Mobile Browser when restored from back/tab cache
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</body>
</html>

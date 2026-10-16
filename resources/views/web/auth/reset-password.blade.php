<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password | Verixa</title>
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
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-400: #2dd4bf;
            --teal-300: #5eead4;
            --teal-glow: rgba(20, 184, 166, 0.45);
            --emerald-400: #7C3AED;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
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
            margin-bottom: 28px;
        }

        .brand-logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
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

        .password-wrapper {
            position: relative;
        }

        .toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-tertiary);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-eye:hover { color: var(--teal-400); }

        .strength-bar {
            height: 3px;
            border-radius: 4px;
            background: rgba(255,255,255,0.08);
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }

        .strength-label {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 5px;
            font-weight: 600;
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
            margin-top: 8px;
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

        .requirements {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 6px;
            line-height: 1.5;
        }

        .requirements span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-right: 10px;
        }

        .req-ok { color: var(--emerald-400); }

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
        }

        .back-link a { color: var(--teal-300); font-weight: 700; text-decoration: none; }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="ambient-bg" aria-hidden="true"></div>
    <div class="dot-grid" aria-hidden="true"></div>

    <div class="auth-card">
        <div class="brand-header">
            <div class="brand-logo-container">
                <x-brand-logo size="60" :glow="true" :badge="false" />
            </div>
            <h1 class="brand-title">Verixa</h1>
            <div class="service-badge">🔐 Set New Password</div>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="alert-error">
                <strong>⚠️ Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form id="resetForm" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div style="margin-bottom: 16px;">
                <label class="form-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email"
                    class="form-input"
                    placeholder="your@email.com"
                    @if($email) readonly @endif
                    style="{{ $email ? 'opacity: 0.65; cursor: default;' : '' }}"
                >
            </div>

            <div style="margin-bottom: 8px;">
                <label class="form-label" for="password">New Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="form-input"
                        placeholder="Minimum 8 characters"
                        style="padding-right: 46px;"
                        oninput="checkStrength(this.value)"
                    >
                    <button type="button" class="toggle-eye" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                <div class="strength-label" id="strength-label"></div>
            </div>

            <div style="margin-bottom: 22px;">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="form-input"
                        placeholder="Repeat new password"
                        style="padding-right: 46px;"
                    >
                    <button type="button" class="toggle-eye" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle confirm visibility">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" id="btn-submit" class="btn-submit">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Reset Password
            </button>
        </form>

        <div class="back-link">
            ← Back to <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.innerHTML = isText
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
        }

        function checkStrength(password) {
            const fill = document.getElementById('strength-fill');
            const label = document.getElementById('strength-label');
            let score = 0;
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            const levels = [
                { pct: 0, color: 'transparent', text: '' },
                { pct: 20, color: '#ef4444', text: 'Weak' },
                { pct: 40, color: '#f97316', text: 'Fair' },
                { pct: 60, color: '#eab308', text: 'Good' },
                { pct: 80, color: '#22d3ee', text: 'Strong' },
                { pct: 100, color: '#7C3AED', text: 'Very Strong' },
            ];

            const level = levels[score] || levels[0];
            fill.style.width = level.pct + '%';
            fill.style.background = level.color;
            label.textContent = level.text;
            label.style.color = level.color;
        }

        document.getElementById('resetForm').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.innerHTML = '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Resetting...';
        });

        // Auto-reload on iOS Safari / Mobile Browser when restored from back/tab cache
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>

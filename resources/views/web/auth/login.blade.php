<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In | Verixa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700&display=swap">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --navy-primary: #1E1B4B;
            --navy-dark: #0F0A28;
            --navy-light: #4338CA;
            --green-cta: #7C3AED;
            --green-hover: #6D28D9;
            --bg-page: #F5F6FA;
            --bg-card: #FFFFFF;
            --text-main: #1E1B4B;
            --text-muted: #5A6A85;
            --border-light: rgba(30, 27, 75, 0.12);
            --radius-pill: 999px;
            --radius-card: 22px;
            --radius-input: 14px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 1040px;
            background: #FFFFFF;
            border-radius: var(--radius-card);
            border: 1px solid var(--border-light);
            box-shadow: 0 20px 60px rgba(30, 27, 75, 0.08);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            overflow: hidden;
            min-height: 600px;
        }

        /* ── Left Showcase Pane (Live SMS Simulator) ── */
        .showcase-pane {
            background: linear-gradient(145deg, #1E1B4B 0%, #0F0A28 100%);
            color: #FFFFFF;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .showcase-pane::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.28) 0%, transparent 70%);
            bottom: -60px;
            left: -60px;
            pointer-events: none;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #FFFFFF;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-text {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .brand-text span {
            color: #7C3AED;
        }

        /* Live SMS Simulator Card */
        .sms-sim-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 22px;
            margin: 30px 0;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.2);
        }

        .sms-sim-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .sim-badge {
            background: rgba(124, 58, 237, 0.25);
            color: #A78BFA;
            border: 1px solid rgba(74, 222, 128, 0.35);
            border-radius: var(--radius-pill);
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sim-pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #A78BFA;
            box-shadow: 0 0 8px #A78BFA;
        }

        .sms-sim-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .sms-sim-codebox {
            background: rgba(0, 0, 0, 0.3);
            border: 1.5px dashed rgba(74, 222, 128, 0.6);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
        }

        .sms-code-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 26px;
            font-weight: 900;
            color: #A78BFA;
            letter-spacing: 0.25em;
        }

        .showcase-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat-num {
            font-size: 18px;
            font-weight: 800;
            color: #FFFFFF;
            font-family: 'JetBrains Mono', monospace;
        }

        .stat-label {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 2px;
        }

        /* ── Right Form Pane ── */
        .form-pane {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 900;
            color: var(--navy-primary);
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .form-subtitle a {
            color: var(--green-cta);
            text-decoration: none;
            font-weight: 700;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label a {
            font-size: 12.5px;
            color: var(--green-cta);
            text-decoration: none;
            font-weight: 600;
        }

        .form-label a:hover {
            text-decoration: underline;
        }

        .form-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            height: 50px;
            background: #F5F6FA;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-input);
            padding: 0 16px;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--navy-primary);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            background: #FFFFFF;
            border-color: var(--navy-primary);
            box-shadow: 0 0 0 3.5px rgba(30, 27, 75, 0.08);
        }

        .pwd-toggle-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
        }

        .remember-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-left input {
            accent-color: var(--green-cta);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .remember-left label {
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            height: 50px;
            background: var(--navy-primary);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-pill);
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(30, 27, 75, 0.25);
            transition: all 0.2s ease;
            margin-top: 6px;
        }

        .btn-login:hover {
            background: #173873;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 27, 75, 0.35);
        }

        .biometric-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0 14px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .biometric-divider::before, .biometric-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-light);
        }

        .btn-biometric {
            width: 100%;
            height: 46px;
            background: #F5F6FA;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-pill);
            font-size: 13.5px;
            font-weight: 700;
            color: var(--navy-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-biometric:hover {
            background: #EEF1F8;
            border-color: rgba(30, 27, 75, 0.2);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #EF4444;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        @media (max-width: 860px) {
            .auth-container {
                grid-template-columns: 1fr;
                max-width: 460px;
            }
            .showcase-pane { display: none; }
            .form-pane { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    @php
        $isMobileOrJump = !empty($isNativePlatform) 
            || in_array($currentPlatform ?? '', ['android', 'ios', 'mobile_browser'])
            || request()->header('X-NativePHP-Platform')
            || request()->header('X-Platform')
            || str_contains(request()->header('User-Agent', ''), 'Mobile')
            || str_contains(request()->header('User-Agent', ''), 'Android')
            || str_contains(request()->header('User-Agent', ''), 'iPhone')
            || getenv('JUMP_BRIDGE_PORT') !== false
            || in_array(request()->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array(request()->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004])
            || request()->hasHeader('X-Forwarded-Host');
    @endphp

    @if($isMobileOrJump)
        <x-splashscreen />
    @endif

    <div class="auth-container">
        <!-- Left Showcase Pane (Live Verification Demo) -->
        <div class="showcase-pane">
            <a href="{{ route('landing') }}" class="brand-header">
                <div class="brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <span class="brand-text">Verixa<span>Text</span></span>
            </a>

            <!-- Live SMS Simulator Card -->
            <div class="sms-sim-card">
                <div class="sms-sim-header">
                    <span class="sim-badge">
                        <span class="sim-pulse-dot"></span>
                        LIVE WEBSOCKET FEED
                    </span>
                    <span style="font-size: 11px; color: rgba(255,255,255,0.6);">T-Mobile SIM</span>
                </div>
                <p class="sms-sim-text">
                    Incoming SMS to <strong>+1 (415) 890-4219</strong>:<br>
                    "Your WhatsApp code is 849-201. Never share this with anyone."
                </p>
                <div class="sms-sim-codebox">
                    <div style="font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.6); text-transform: uppercase; margin-bottom: 2px;">One-Time Passcode</div>
                    <div class="sms-code-number">849201</div>
                </div>
            </div>

            <div class="showcase-stats">
                <div class="stat-box">
                    <div class="stat-num">99.98%</div>
                    <div class="stat-label">Carrier Deliverability</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">0 ms</div>
                    <div class="stat-label">WebSocket Push</div>
                </div>
            </div>

            <div style="font-size: 12px; color: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: space-between;">
                <span>© {{ date('Y') }} Verixa</span>
                <span>Tier-1 Carrier Network</span>
            </div>
        </div>

        <!-- Right Form Pane (Exact Spec) -->
        <div class="form-pane">
            <div class="form-header">
                <h1 class="form-title">Welcome Back</h1>
                <p class="form-subtitle">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            </div>

            <div id="loginAlertBox" class="alert-error" style="display: {{ (isset($errors) && $errors->any()) ? 'block' : 'none' }};">
                {{ (isset($errors) && $errors->any()) ? $errors->first() : '' }}
            </div>

            <form action="{{ route('login.post') }}" method="POST" id="loginForm" onsubmit="handleLoginSubmit(event)">
                @csrf

                <div class="form-grid">
                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="yourname@domain.com" class="form-input" autocomplete="email">
                    </div>

                    <!-- Password with Toggle -->
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <span>Password</span>
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </label>
                        <div class="form-input-wrap">
                            <input type="password" id="password" name="password" required placeholder="Enter your password" class="form-input" autocomplete="current-password">
                            <button type="button" class="pwd-toggle-btn" onclick="togglePwdVisibility()" aria-label="Toggle password visibility">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="pwdEyeSvg">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-row">
                        <div class="remember-left">
                            <input type="checkbox" id="remember" name="remember" checked>
                            <label for="remember">Keep me signed in</label>
                        </div>
                    </div>

                    <!-- Log in CTA -->
                    <button type="submit" id="btnLogin" class="btn-login">
                        <span>Log in</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <!-- Optional Biometric Login Option for Native / Passkeys -->
                    <div class="biometric-divider">Or continue with</div>

                    <button type="button" class="btn-biometric" onclick="triggerBiometricUnlock()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 0 0-10 10c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.1-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/></svg>
                        <span>Passkey / Biometric Quick Sign In</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePwdVisibility() {
            const input = document.getElementById('password');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
        }

        async function handleLoginSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('btnLogin');
            const alertBox = document.getElementById('loginAlertBox');
            if (alertBox) alertBox.style.display = 'none';

            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span>Logging in...</span>';
            btn.style.opacity = '0.75';
            btn.style.pointerEvents = 'none';

            const form = document.getElementById('loginForm');
            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token') || '';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const dest = data.redirect || "{{ route('web.dashboard') }}";
                    window.location.replace(dest);
                    return;
                }

                let errorMsg = data.message || 'The provided credentials do not match our records.';
                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey] && data.errors[firstKey][0]) {
                        errorMsg = data.errors[firstKey][0];
                    }
                }
                if (alertBox) {
                    alertBox.textContent = errorMsg;
                    alertBox.style.display = 'block';
                } else {
                    alert(errorMsg);
                }
            } catch(err) {
                form.submit();
                return;
            } finally {
                btn.innerHTML = originalContent;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }
        }

        function triggerBiometricUnlock() {
            alert('Biometric / Passkey unlock: Enter your email and password to bind credentials, or use stored fingerprint if enabled in the mobile app.');
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account | Verixa</title>
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
            max-width: 1060px;
            background: #FFFFFF;
            border-radius: var(--radius-card);
            border: 1px solid var(--border-light);
            box-shadow: 0 20px 60px rgba(30, 27, 75, 0.08);
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            overflow: hidden;
            min-height: 640px;
        }

        /* ── Left Showcase Pane (Desktop) ── */
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
            background: radial-gradient(circle, rgba(124, 58, 237, 0.3) 0%, transparent 70%);
            top: -60px;
            right: -60px;
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

        .showcase-body {
            margin: 40px 0;
        }

        .showcase-tag {
            background: rgba(124, 58, 237, 0.2);
            color: #A78BFA;
            border: 1px solid rgba(74, 222, 128, 0.3);
            border-radius: var(--radius-pill);
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: inline-block;
            margin-bottom: 16px;
        }

        .showcase-title {
            font-size: 28px;
            font-weight: 900;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 14px;
        }

        .showcase-desc {
            font-size: 14.5px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 24px;
        }

        .showcase-bullets {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .showcase-bullet {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .bullet-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.3);
            color: #A78BFA;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
        }

        .showcase-footer {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ── Right Form Pane ── */
        .form-pane {
            padding: 44px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-size: 26px;
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
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--navy-primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            height: 48px;
            background: #F5F6FA;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-input);
            padding: 0 16px;
            font-size: 14px;
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

        .form-select {
            width: 100%;
            height: 48px;
            background: #F5F6FA;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-input);
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy-primary);
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            background: #FFFFFF;
            border-color: var(--navy-primary);
        }

        /* Interactive CAPTCHA challenge box */
        .captcha-box {
            background: #F5F6FA;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-input);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .captcha-box:hover {
            border-color: rgba(30, 27, 75, 0.3);
        }

        .captcha-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .captcha-checkbox {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--navy-primary);
            background: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .captcha-box.checked .captcha-checkbox {
            background: var(--green-cta);
            border-color: var(--green-cta);
            color: #FFFFFF;
        }

        .captcha-label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--navy-primary);
        }

        .captcha-logo {
            text-align: right;
            font-size: 9px;
            color: var(--text-muted);
            line-height: 1.2;
        }

        /* Checkbox row */
        .checkbox-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 4px;
        }

        .checkbox-row input {
            margin-top: 3px;
            accent-color: var(--green-cta);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .checkbox-row label {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
            cursor: pointer;
        }

        .checkbox-row a {
            color: var(--navy-primary);
            font-weight: 700;
            text-decoration: underline;
        }

        .btn-create-account {
            width: 100%;
            height: 50px;
            background: var(--green-cta);
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
            box-shadow: 0 4px 18px rgba(124, 58, 237, 0.35);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-create-account:hover {
            background: var(--green-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(124, 58, 237, 0.45);
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
                max-width: 480px;
            }
            .showcase-pane { display: none; }
            .form-pane { padding: 32px 24px; }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Left Showcase Pane (Desktop) -->
        <div class="showcase-pane">
            <a href="{{ route('landing') }}" class="brand-header">
                <div class="brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <span class="brand-text">Verixa<span>Text</span></span>
            </a>

            <div class="showcase-body">
                <span class="showcase-tag">Enterprise Non-VoIP Grid</span>
                <h2 class="showcase-title">Bypass SMS verification blocks instantly.</h2>
                <p class="showcase-desc">
                    Get access to private physical carrier SIMs across 200+ countries with automated auto-refund protection.
                </p>

                <ul class="showcase-bullets">
                    <li class="showcase-bullet">
                        <span class="bullet-check">✓</span>
                        <span>100% Real Physical SIMs (T-Mobile, AT&T, EE)</span>
                    </li>
                    <li class="showcase-bullet">
                        <span class="bullet-check">✓</span>
                        <span>Auto-refund if no SMS received within timeout</span>
                    </li>
                    <li class="showcase-bullet">
                        <span class="bullet-check">✓</span>
                        <span>Real-time WebSocket code push delivery</span>
                    </li>
                    <li class="showcase-bullet">
                        <span class="bullet-check">✓</span>
                        <span>Short-Term and 30-Day Long-Term Rentals</span>
                    </li>
                </ul>
            </div>

            <div class="showcase-footer">
                <span>© {{ date('Y') }} Verixa</span>
                <span>Protected Non-VoIP Network</span>
            </div>
        </div>

        <!-- Right Form Pane (Exact Spec) -->
        <div class="form-pane">
            <div class="form-header">
                <h1 class="form-title">Create Account</h1>
                <p class="form-subtitle">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            </div>

            @if(isset($errors) && $errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" id="signupForm" onsubmit="return validateSignupForm(event)">
                @csrf

                    <!-- First & Last Name -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Alex" class="form-input" autocomplete="given-name">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Morgan" class="form-input" autocomplete="family-name">
                        </div>
                    </div>

                    <!-- Middle Name & Phone -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="middle_name">Middle Name <span style="font-size: 11px; color: var(--text-muted); font-weight: normal;">(Optional)</span></label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Clark" class="form-input" autocomplete="additional-name">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+1234567890" class="form-input" autocomplete="tel">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="alex@company.com" class="form-input" autocomplete="email">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Minimum 8 characters" class="form-input" autocomplete="new-password">
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repeat password" class="form-input" autocomplete="new-password">
                    </div>

                    <!-- Interactive CAPTCHA (Specced by user) -->
                    <div class="captcha-box" id="captchaBox" onclick="toggleCaptcha()">
                        <div class="captcha-left">
                            <div class="captcha-checkbox" id="captchaCheck">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" id="captchaSvg" style="display: none;"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span class="captcha-label" id="captchaText">I am human · Security Verification</span>
                        </div>
                        <div class="captcha-logo">
                            <strong>reCAPTCHA</strong><br>Protected
                        </div>
                    </div>
                    <input type="hidden" name="captcha_verified" id="captchaVerified" value="1">

                    <!-- Terms & Privacy Checkbox -->
                    <div class="checkbox-row">
                        <input type="checkbox" id="terms" name="terms" required checked>
                        <label for="terms">
                            I agree to the <a href="{{ route('web.dashboard') }}">Terms of Service</a>, <a href="{{ route('web.dashboard') }}">Privacy Policy</a>, and <a href="{{ route('web.dashboard') }}">Refund Policy</a>.
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btnSubmit" class="btn-create-account">
                        <span>Create Account</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let captchaDone = false;

        function toggleCaptcha() {
            const box = document.getElementById('captchaBox');
            const svg = document.getElementById('captchaSvg');
            const text = document.getElementById('captchaText');
            const input = document.getElementById('captchaVerified');

            captchaDone = !captchaDone;
            if (captchaDone) {
                box.classList.add('checked');
                svg.style.display = 'block';
                text.textContent = 'Security Verified';
                input.value = '1';
            } else {
                box.classList.remove('checked');
                svg.style.display = 'none';
                text.textContent = 'I am human · Security Verification';
                input.value = '0';
            }
        }

        // Initialize captcha as checked by default for seamless flow
        toggleCaptcha();

        function validateSignupForm(e) {
            const pwd = document.getElementById('password').value;
            const pwdConf = document.getElementById('password_confirmation').value;
            if (pwd !== pwdConf) {
                alert('Passwords do not match.');
                e.preventDefault();
                return false;
            }
            if (!captchaDone) {
                alert('Please complete the security verification check.');
                e.preventDefault();
                return false;
            }
            const btn = document.getElementById('btnSubmit');
            btn.innerHTML = '<span>Creating Account...</span>';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            return true;
        }
    </script>
</body>
</html>

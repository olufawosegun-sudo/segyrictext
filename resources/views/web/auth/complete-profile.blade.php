@php
    $b64File = public_path('images/Verixa-b64.txt');
    $logoSrc = file_exists($b64File) ? trim(file_get_contents($b64File)) : '/images/verixa-thumb.png';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complete Your Profile • Verixa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --navy-950: #07101F;
            --navy-900: #0B192E;
            --navy-800: #1E1B4B;
            --navy-700: #163A75;
            --emerald-600: #059669;
            --emerald-500: #7C3AED;
            --emerald-400: #34D399;
            --emerald-300: #6EE7B7;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(180deg, #07101F 0%, #0B192E 50%, #1E1B4B 100%);
            color: #fff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: calc(var(--safe-top) + 24px) 20px calc(var(--safe-bottom) + 24px);
        }

        .auth-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle 500px at 50% 15%, rgba(16, 185, 129, 0.18) 0%, transparent 70%),
                radial-gradient(circle 450px at 20% 80%, rgba(30, 27, 75, 0.45) 0%, transparent 65%);
        }

        .dot-pattern {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.035;
            background-image: radial-gradient(circle, #7C3AED 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .profile-container {
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 1;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
        }

        .logo-box img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 50%;
            display: block;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--emerald-400);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }

        .title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
            color: #FFFFFF;
        }

        .subtitle {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
        }

        .card {
            background: rgba(11, 25, 46, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 30px 26px;
            box-shadow: 0 20px 50px rgba(7, 16, 31, 0.6);
        }

        .error-alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1.5px solid rgba(239, 68, 68, 0.35);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 12.5px;
            color: #fca5a5;
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .error-alert svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 440px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label span.opt {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.45);
            font-weight: normal;
        }

        .form-input {
            width: 100%;
            height: 48px;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--emerald-400);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 500;
        }

        .field-hint {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 2px;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #7C3AED 0%, #059669 100%);
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(16, 185, 129, 0.45);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .auth-footer {
            margin-top: 22px;
            text-align: center;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-logout {
            background: none;
            border: none;
            color: var(--emerald-400);
            font-weight: 700;
            font-size: inherit;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
        }

        .btn-logout:hover {
            color: var(--emerald-300);
        }
    </style>
</head>
<body>
    <div class="auth-bg"></div>
    <div class="dot-pattern"></div>

    <div class="profile-container">
        <div class="brand-header">
            <div class="logo-box">
                <img src="{{ $logoSrc }}" 
                     alt="Verixa" 
                     loading="eager" 
                     decoding="sync"
                     onerror="this.style.display='none'; document.getElementById('profileLogoSvgFallback').style.display='block';">
                <svg id="profileLogoSvgFallback" viewBox="0 0 100 100" style="display: none; width: 44px; height: 44px; border-radius: 50%;">
                    <circle cx="50" cy="50" r="46" fill="#0B192E" stroke="#7C3AED" stroke-width="4"/>
                    <path d="M30 35 L70 35 L70 48 L45 48 L45 52 L70 52 L70 65 L30 65" stroke="#7C3AED" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="step-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Account Setup • Required
            </div>
            <h1 class="title">Complete Your Profile</h1>
            <p class="subtitle">Please provide your legal name and primary mobile number to complete your registration and access services.</p>
        </div>

        <div class="card">
            @if ($errors->any())
                <div class="error-alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('profile.complete.store') }}" method="POST" id="completeProfileForm">
                @csrf

                <div class="form-grid">
                    <!-- First & Last Name Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                value="{{ old('first_name', $firstName) }}" 
                                required 
                                placeholder="e.g. Alex" 
                                class="form-input" 
                                autocomplete="given-name"
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                value="{{ old('last_name', $lastName) }}" 
                                required 
                                placeholder="e.g. Morgan" 
                                class="form-input" 
                                autocomplete="family-name"
                            >
                        </div>
                    </div>

                    <!-- Middle Name Row -->
                    <div class="form-group">
                        <label class="form-label" for="middle_name">
                            Middle Name 
                            <span class="opt">Optional</span>
                        </label>
                        <input 
                            type="text" 
                            id="middle_name" 
                            name="middle_name" 
                            value="{{ old('middle_name', $middleName) }}" 
                            placeholder="e.g. Clark" 
                            class="form-input" 
                            autocomplete="additional-name"
                        >
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $phone) }}" 
                            required 
                            placeholder="+1234567890" 
                            class="form-input" 
                            autocomplete="tel"
                        >
                        <span class="field-hint">Include your country code with "+" prefix (e.g. +1 for US/Canada, +234 for Nigeria, +44 for UK).</span>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span>Save & Continue to App</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="auth-footer">
            Logged in as <strong>{{ $user->email }}</strong> &bull; 
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="source" value="desktop">
                <button type="submit" class="btn-logout">Sign out</button>
            </form>
        </div>
    </div>
</body>
</html>

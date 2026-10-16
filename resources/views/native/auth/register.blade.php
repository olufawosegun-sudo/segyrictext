@php
    $logoSrc = asset('images/verixa-thumb.png');
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
    <title>Create Account • Verixa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --primary: #1E1B4B;
            --accent: #7C3AED;
            --bg: #0B0F1A;
            --text: #FFFFFF;
            --text-dim: #94A3B8;
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.1);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: calc(var(--safe-top) + 24px) 24px calc(var(--safe-bottom) + 24px);
        }

        .header {
            margin-bottom: 32px;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-dim);
        }

        .form {
            flex: 1;
        }

        .input-group {
            margin-bottom: 16px;
        }

        .input {
            width: 100%;
            padding: 16px 20px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            color: var(--text);
            font-size: 16px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .input::placeholder {
            color: var(--text-dim);
        }

        .input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(16, 185, 129, 0.05);
        }

        .requirements {
            margin: 20px 0;
            padding-left: 20px;
        }

        .requirements li {
            font-size: 13px;
            color: var(--text-dim);
            margin-bottom: 6px;
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: var(--text);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn:active {
            transform: scale(0.98);
            opacity: 0.9;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .terms {
            font-size: 12px;
            color: var(--text-dim);
            text-align: center;
            margin: 20px 0;
        }

        .terms a {
            color: #3B82F6;
            text-decoration: none;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-dim);
        }

        .footer-link a {
            color: #3B82F6;
            text-decoration: none;
            font-weight: 600;
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #FCA5A5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 16px;
            display: none;
        }

        .error.show {
            display: block;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Create Account</h1>
            <p class="subtitle">Enter your details below to create your account</p>
        </div>

        <form action="{{ route('register.post', [], false) }}" method="POST" class="form" id="registerForm">
            @csrf
            
            <div id="errorMessage" class="error"></div>

            <div class="input-group">
                <input 
                    type="email" 
                    name="email" 
                    class="input" 
                    placeholder="Enter email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="input-group">
                <input 
                    type="text" 
                    name="first_name" 
                    class="input" 
                    placeholder="Enter username"
                    value="{{ old('first_name') }}"
                    required
                    autocomplete="given-name"
                >
            </div>

            <div class="input-group">
                <input 
                    type="text" 
                    name="last_name" 
                    class="input" 
                    placeholder="Last name"
                    value="{{ old('last_name') }}"
                    autocomplete="family-name"
                    style="display: none;"
                >
                <input 
                    type="tel" 
                    name="phone" 
                    class="input" 
                    placeholder="Phone number"
                    value="{{ old('phone') }}"
                    required
                    autocomplete="tel"
                >
            </div>

            <div class="input-group">
                <input 
                    type="password" 
                    name="password" 
                    class="input" 
                    placeholder="Password"
                    required
                    autocomplete="new-password"
                    minlength="8"
                >
            </div>

            <div class="input-group" style="display: none;">
                <input 
                    type="password" 
                    name="password_confirmation" 
                    class="input" 
                    placeholder="Confirm Password"
                    autocomplete="new-password"
                >
            </div>

            <ul class="requirements">
                <li>8+ characters</li>
                <li>1+ number</li>
                <li>1+ uppercase letter</li>
                <li>1+ lowercase letter</li>
            </ul>

            <div class="terms">
                By clicking create account, you agree to our 
                <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
            </div>

            <button type="submit" class="btn" id="registerBtn">
                <span id="btnText">Create Account</span>
            </button>

            <div class="footer-link">
                Already have an account? <a href="{{ route('login') }}">Login</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const btn = document.getElementById('registerBtn');
        const btnText = document.getElementById('btnText');
        const errorDiv = document.getElementById('errorMessage');
        const passwordInput = form.querySelector('input[name="password"]');
        const passwordConfirm = form.querySelector('input[name="password_confirmation"]');
        const lastNameInput = form.querySelector('input[name="last_name"]');

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }

        function hideError() {
            errorDiv.classList.remove('show');
        }

        // Auto-copy password to confirmation
        passwordInput.addEventListener('input', function() {
            passwordConfirm.value = this.value;
        });

        // In-app AJAX submission: Keeps navigation strictly inside NativePHP WKWebView and prevents opening Safari
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const firstName = form.querySelector('input[name="first_name"]').value;
            if (!lastNameInput.value) {
                lastNameInput.value = firstName;
            }
            
            hideError();
            btn.disabled = true;
            btnText.innerHTML = '<span class="spinner"></span>Creating account...';
            
            try {
                sessionStorage.setItem('Verixa_native_splash_seen', '1');
                localStorage.setItem('Verixa_native_splash_seen', '1');
            } catch(e) {}
            
            try {
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                const response = await fetch("{{ route('register.post', [], false) }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btnText.innerHTML = 'Success!';

                    // CRITICAL: Clear ALL payment-related data immediately
                    try {
                        localStorage.removeItem('Verixa_pending_payment');
                        localStorage.removeItem('pending_deposit_ref');
                        localStorage.removeItem('pending_deposit_amt');
                        localStorage.removeItem('pending_deposit_time');
                        console.log('[REGISTER] Cleared all payment localStorage');
                    } catch(ex) {}

                    // Set login suppression flag - prevents payment auto-recovery
                    try {
                        sessionStorage.setItem('Verixa_just_logged_in', String(Date.now()));
                        sessionStorage.setItem('Verixa_page_load_time', String(Date.now()));
                        sessionStorage.setItem('Verixa_registration_complete', '1');
                        console.log('[REGISTER] Set suppression flags');
                    } catch(ex) {}
                    
                    // NUCLEAR OPTION: If iframe exists on current page, destroy it NOW
                    // This prevents Safari from caching it
                    try {
                        const existingIframe = document.getElementById('complete-payment-iframe');
                        if (existingIframe) {
                            existingIframe.src = 'about:blank';
                            existingIframe.remove();
                            console.log('[REGISTER] Destroyed existing iframe before navigation');
                        }
                    } catch(ex) {}

                    const destination = data.redirect || "/mobile";
                    
                    // Small delay to ensure cleanup completes
                    setTimeout(function() {
                        window.location.replace(destination);
                    }, 100);
                } else {
                    btn.disabled = false;
                    btnText.innerHTML = 'Create Account';
                    const errMsg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Registration failed. Please check your information.');
                    showError(errMsg);
                }
            } catch (err) {
                btn.disabled = false;
                btnText.innerHTML = 'Create Account';
                showError('Registration failed. Please check your connection and try again.');
            }
        });

        // Show validation errors
        @if($errors->any())
            showError('{{ $errors->first() }}');
        @endif
    </script>
</body>
</html>

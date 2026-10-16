<?php
    $logoSrc = asset('images/verixa-thumb.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1E1B4B">
    <title>Welcome Back • Verixa</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/verixa-thumb.png')); ?>">
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
            --primary-light: #163A75;
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
            padding: calc(var(--safe-top) + 40px) 24px calc(var(--safe-bottom) + 24px);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            border-radius: 20px;
        }

        .logo-text {
            font-size: 32px;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -0.02em;
        }

        .welcome {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            text-align: center;
            margin-bottom: 40px;
        }

        .form {
            flex: 1;
            display: flex;
            flex-direction: column;
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

        .input-password {
            position: relative;
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

        .link {
            color: #3B82F6;
            text-decoration: none;
            font-size: 14px;
            text-align: center;
            display: block;
            margin-top: 20px;
        }

        .footer-link {
            margin-top: auto;
            padding-top: 40px;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
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
        <div class="logo-section">
            <img src="<?php echo e($logoSrc); ?>" alt="Verixa" class="logo">
            <div class="logo-text">Verixa</div>
        </div>

        <h1 class="welcome">Welcome Back!</h1>

        <form action="<?php echo e(route('login.post', [], false)); ?>" method="POST" class="form" id="loginForm">
            <?php echo csrf_field(); ?>
            
            <div id="errorMessage" class="error"></div>

            <div class="input-group">
                <input 
                    type="email" 
                    name="email" 
                    class="input" 
                    placeholder="Enter address"
                    value="<?php echo e(old('email')); ?>"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="input-group">
                <input 
                    type="password" 
                    name="password" 
                    class="input" 
                    placeholder="Password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn" id="loginBtn">
                <span id="btnText">LOG IN</span>
            </button>

            <a href="#" class="link">Forgot password?</a>

            <div class="footer-link">
                <a href="<?php echo e(route('register', [], false)); ?>" class="btn btn-outline">Create Account</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        const errorDiv = document.getElementById('errorMessage');

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }

        function hideError() {
            errorDiv.classList.remove('show');
        }

        // In-app AJAX submission: Keeps navigation strictly inside NativePHP WKWebView and prevents opening Safari
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideError();
            btn.disabled = true;
            btnText.innerHTML = '<span class="spinner"></span>Signing in...';
            
            try {
                sessionStorage.setItem('Verixa_native_splash_seen', '1');
                localStorage.setItem('Verixa_native_splash_seen', '1');
            } catch(e) {}

            try {
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                const response = await fetch("<?php echo e(route('login.post', [], false)); ?>", {
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

                    // Clear any stale pending payment data from localStorage so it
                    // does NOT auto-trigger checkout.paystack.com after login
                    try {
                        localStorage.removeItem('segyric_pending_payment');
                        localStorage.removeItem('verixa_pending_payment');
                        localStorage.removeItem('Verixa_pending_payment');
                        localStorage.removeItem('pending_deposit_ref');
                        localStorage.removeItem('pending_deposit_amt');
                        localStorage.removeItem('pending_deposit_time');
                        console.log('[LOGIN] Cleared all payment localStorage');
                    } catch(ex) {}

                    // Set login suppression flag — payment-sync.js will check this
                    // and skip auto-recovery within the suppression window
                    try {
                        const now = String(Date.now());
                        sessionStorage.setItem('segyric_just_logged_in', now);
                        sessionStorage.setItem('verixa_just_logged_in', now);
                        sessionStorage.setItem('Verixa_just_logged_in', now);
                        sessionStorage.setItem('segyric_page_load_time', now);
                        sessionStorage.setItem('verixa_page_load_time', now);
                        sessionStorage.setItem('Verixa_page_load_time', now);
                        console.log('[LOGIN] Set suppression flags');
                    } catch(ex) {}
                    
                    // NUCLEAR OPTION: Destroy any existing iframe before navigation
                    try {
                        const existingIframe = document.getElementById('complete-payment-iframe');
                        if (existingIframe) {
                            existingIframe.src = 'about:blank';
                            existingIframe.remove();
                            console.log('[LOGIN] Destroyed existing iframe before navigation');
                        }
                    } catch(ex) {}

                    const destination = data.redirect || "/mobile";
                    
                    // Small delay to ensure cleanup completes
                    setTimeout(function() {
                        window.location.replace(destination);
                    }, 100);
                } else {
                    btn.disabled = false;
                    btnText.innerHTML = 'LOG IN';
                    const errMsg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'The provided credentials do not match our records.');
                    showError(errMsg);
                }
            } catch (err) {
                btn.disabled = false;
                btnText.innerHTML = 'LOG IN';
                showError('Sign in failed. Please check your connection and credentials.');
            }
        });

        // Show server-rendered validation errors if any
        <?php if($errors->any()): ?>
            showError('<?php echo e($errors->first()); ?>');
        <?php endif; ?>
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\segyrictext\resources\views/native/auth/login.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Verixa Text</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            width: 100%;
            height: 100%;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            background: #07101F;
            overflow: hidden;
            user-select: none;
            -webkit-user-select: none;
        }

        .splash-container {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .splash-container.fade-out {
            opacity: 0;
            transform: scale(1.05);
            pointer-events: none;
        }

        /* Ambient Backdrop */
        .ambient-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background: 
                radial-gradient(circle 600px at 50% 50%, rgba(16, 185, 129, 0.25) 0%, transparent 65%),
                radial-gradient(circle 450px at 50% 50%, rgba(6, 182, 212, 0.18) 0%, transparent 60%),
                radial-gradient(circle at center, #0B192E 0%, #07101F 100%);
        }

        .dot-matrix {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.12;
            background-image: radial-gradient(circle, #7C3AED 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }

        /* Centerpiece Container */
        .logo-centerpiece {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: popIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .logo-ring-wrapper {
            position: relative;
            width: 170px;
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-ring {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .ring-1 {
            width: 130px;
            height: 130px;
            border: 1.5px stroke rgba(16, 185, 129, 0.45);
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4);
            animation: ringPulse 2.2s ease-in-out infinite alternate;
        }

        .ring-2 {
            width: 154px;
            height: 154px;
            border: 1px dashed rgba(6, 182, 212, 0.4);
            animation: ringRotate 20s linear infinite;
        }

        .ring-3 {
            width: 170px;
            height: 170px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .logo-badge-container {
            position: relative;
            z-index: 3;
            animation: floatLogo 3.2s ease-in-out infinite alternate;
        }

        @keyframes popIn {
            0% { transform: scale(0.85); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes ringPulse {
            0% { transform: scale(0.96); opacity: 0.5; }
            100% { transform: scale(1.08); opacity: 0.95; }
        }

        @keyframes ringRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes floatLogo {
            0% { transform: translateY(-4px); }
            100% { transform: translateY(4px); }
        }
    </style>
</head>
<body>
    <div class="ambient-bg"></div>
    <div class="dot-matrix"></div>

    <div class="splash-container" id="splashScreen" onclick="proceedToLogin()">
        <div class="logo-centerpiece">
            <div class="logo-ring-wrapper">
                <div class="logo-ring ring-3"></div>
                <div class="logo-ring ring-2"></div>
                <div class="logo-ring ring-1"></div>
                <div class="logo-badge-container">
                    <x-brand-logo size="110" :glow="true" :badge="true" />
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var splash = document.getElementById('splashScreen');
            var dismissed = false;

            var timer = setTimeout(function() {
                proceedToLogin();
            }, 1400);

            window.proceedToLogin = function() {
                if (dismissed) return;
                dismissed = true;
                clearTimeout(timer);
                splash.classList.add('fade-out');
                setTimeout(function() {
                    window.location.href = "{{ route('login') }}";
                }, 400);
            };
        })();
    </script>
</body>
</html>

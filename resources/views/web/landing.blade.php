<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Verixa — Real Non-VoIP Carrier Virtual Numbers from 200+ countries. Instant SMS verification for WhatsApp, Telegram, OpenAI, Google and more with auto-refund guarantee.">
    <title>Verixa — Real Non-VoIP Numbers for Instant SMS Verification</title>
    <link rel="icon" type="image/png" href="{{ asset('images/verixa-thumb.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;600;700;800&display=swap">
    <style>
        /* ═══════════════════════════════════════════════════════════════
           Verixa TEXT — AWARD-WINNING ENTERPRISE DESIGN SYSTEM
           Navy #1E1B4B · Green #7C3AED · White / Light #F5F6FA · Radius 999px
        ═══════════════════════════════════════════════════════════════ */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --navy-primary: #1E1B4B;
            --navy-dark: #0F0A28;
            --navy-deep: #050E20;
            --navy-light: #4338CA;
            --green-cta: #7C3AED;
            --green-hover: #6D28D9;
            --green-glow: rgba(124, 58, 237, 0.25);
            --amber-accent: #F59E0B;
            --bg-page: #F5F6FA;
            --bg-card: #FFFFFF;
            --text-main: #1E1B4B;
            --text-muted: #5A6A85;
            --border-light: rgba(30, 27, 75, 0.1);
            --border-focus: #1E1B4B;
            --radius-pill: 999px;
            --radius-card: 20px;
            --shadow-soft: 0 10px 30px rgba(30, 27, 75, 0.06);
            --shadow-hover: 0 16px 40px rgba(30, 27, 75, 0.12);
        }

        html {
            scroll-behavior: smooth;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-page);
            color: var(--text-main);
        }

        body {
            overflow-x: hidden;
            background: #F5F6FA;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Sticky Navbar (White bg) ── */
        .sticky-navbar {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #FFFFFF;
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 2px 14px rgba(30, 27, 75, 0.05);
            transition: all 0.25s ease;
        }

        .nav-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--navy-primary);
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1E1B4B 0%, #4338CA 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(30, 27, 75, 0.2);
        }

        .brand-name {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--navy-primary);
        }

        .brand-name span {
            color: var(--green-cta);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-muted);
            transition: color 0.15s ease;
        }

        .nav-links a:hover {
            color: var(--navy-primary);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav-login {
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            color: var(--navy-primary);
            border: 1.5px solid var(--navy-primary);
            border-radius: var(--radius-pill);
            padding: 8px 22px;
            transition: all 0.2s ease;
            background: transparent;
        }

        .btn-nav-login:hover {
            background: rgba(30, 27, 75, 0.05);
            transform: translateY(-1px);
        }

        .btn-nav-signup {
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            background: var(--navy-primary);
            border: 1.5px solid var(--navy-primary);
            border-radius: var(--radius-pill);
            padding: 8px 24px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(30, 27, 75, 0.2);
        }

        .btn-nav-signup:hover {
            background: #173873;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 27, 75, 0.3);
        }

        /* ═══════════════════════════════════════════════════
           HERO SECTION — WORLD-CLASS CINEMATIC REDESIGN
           Dark gradient, mesh, glassmorphism, 3D phone
        ═══════════════════════════════════════════════════ */
        .hero-section {
            position: relative;
            padding: 0;
            background: linear-gradient(165deg, #050E20 0%, #0A1A3A 25%, #1E1B4B 50%, #0D2248 75%, #060F24 100%);
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Animated gradient mesh overlay */
        .hero-mesh {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .hero-mesh::before {
            content: '';
            position: absolute;
            width: 900px;
            height: 900px;
            top: -300px;
            right: -200px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.15) 0%, transparent 65%);
            animation: meshDrift1 12s ease-in-out infinite alternate;
        }

        .hero-mesh::after {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            bottom: -200px;
            left: -100px;
            background: radial-gradient(circle, rgba(30, 58, 138, 0.2) 0%, transparent 65%);
            animation: meshDrift2 15s ease-in-out infinite alternate;
        }

        @keyframes meshDrift1 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-80px, 60px) scale(1.15); }
        }

        @keyframes meshDrift2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, -40px) scale(1.1); }
        }

        /* Grid pattern overlay */
        .hero-grid-pattern {
            position: absolute;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 30%, transparent 80%);
        }

        /* Subtle floating particles */
        .hero-particles {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(124, 58, 237, 0.4);
            border-radius: 50%;
            animation: particleFloat 8s ease-in-out infinite;
        }

        .particle:nth-child(2) { width: 2px; height: 2px; top: 20%; left: 15%; animation-delay: 1s; animation-duration: 10s; background: rgba(255,255,255,0.15); }
        .particle:nth-child(3) { width: 4px; height: 4px; top: 60%; left: 80%; animation-delay: 2s; animation-duration: 12s; background: rgba(124, 58, 237, 0.3); }
        .particle:nth-child(4) { width: 2px; height: 2px; top: 40%; left: 50%; animation-delay: 3s; animation-duration: 9s; background: rgba(255,255,255,0.1); }
        .particle:nth-child(5) { width: 3px; height: 3px; top: 80%; left: 25%; animation-delay: 0.5s; animation-duration: 11s; background: rgba(30, 58, 138, 0.4); }
        .particle:nth-child(6) { width: 2px; height: 2px; top: 10%; left: 70%; animation-delay: 4s; animation-duration: 13s; background: rgba(255,255,255,0.08); }
        .particle:nth-child(7) { width: 3px; height: 3px; top: 70%; left: 90%; animation-delay: 2.5s; animation-duration: 10s; background: rgba(124, 58, 237, 0.25); }
        .particle:nth-child(8) { width: 2px; height: 2px; top: 30%; left: 35%; animation-delay: 1.5s; animation-duration: 14s; background: rgba(255,255,255,0.12); }

        @keyframes particleFloat {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.3; }
            25% { transform: translateY(-30px) translateX(15px); opacity: 0.8; }
            50% { transform: translateY(-15px) translateX(-10px); opacity: 0.5; }
            75% { transform: translateY(-40px) translateX(20px); opacity: 0.9; }
        }

        /* Hero inner layout */
        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 100px 32px 80px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 64px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        /* ── Left Content Column ── */
        .hero-content {
            animation: heroFadeUp 0.8s ease-out both;
        }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Trust badge / pill */
        .hero-badge-row {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--radius-pill);
            padding: 7px 18px 7px 8px;
            margin-bottom: 28px;
            animation: heroFadeUp 0.8s ease-out 0.1s both;
        }

        .hero-badge-pill {
            background: rgba(124, 58, 237, 0.2);
            color: #A78BFA;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
        }

        .hero-badge-text {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Massive title with gradient text */
        .hero-title {
            font-size: 58px;
            line-height: 1.08;
            font-weight: 900;
            color: #FFFFFF;
            letter-spacing: -0.035em;
            margin-bottom: 10px;
            animation: heroFadeUp 0.8s ease-out 0.2s both;
        }

        .hero-title-line2 {
            display: block;
            background: linear-gradient(135deg, #A78BFA 0%, #22d3ee 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding-bottom: 6px;
        }

        .hero-subtext {
            font-size: 18px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 12px;
            max-width: 540px;
            animation: heroFadeUp 0.8s ease-out 0.3s both;
        }

        /* Value proposition strip */
        .hero-value-strip {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 36px;
            animation: heroFadeUp 0.8s ease-out 0.35s both;
            flex-wrap: wrap;
        }

        .hero-value-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.75);
        }

        .hero-value-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.2);
            border: 1px solid rgba(74, 222, 128, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A78BFA;
            font-size: 11px;
            flex-shrink: 0;
        }

        /* CTA buttons */
        .hero-ctas {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            animation: heroFadeUp 0.8s ease-out 0.4s both;
        }

        .btn-hero-primary {
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            color: #FFFFFF;
            background: linear-gradient(135deg, #7C3AED 0%, #22c55e 100%);
            padding: 17px 36px;
            border-radius: var(--radius-pill);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.4), 0 8px 32px rgba(124, 58, 237, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-hero-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.3), 0 12px 40px rgba(124, 58, 237, 0.5);
        }

        .btn-hero-primary:hover::before {
            opacity: 1;
        }

        .btn-hero-secondary {
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.06);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 16px 32px;
            border-radius: var(--radius-pill);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Trusted-by logos strip */
        .hero-trust-bar {
            display: flex;
            align-items: center;
            gap: 32px;
            animation: heroFadeUp 0.8s ease-out 0.5s both;
        }

        .trust-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.3);
            white-space: nowrap;
        }

        .trust-logos {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .trust-logo-item {
            font-size: 13px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .trust-logo-item:hover {
            color: rgba(255, 255, 255, 0.6);
        }

        /* ── Right Column: 3D Phone Composition ── */
        .hero-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
            animation: heroFadeUp 0.8s ease-out 0.3s both;
        }

        /* Glow behind phone */
        .phone-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.2) 0%, rgba(30, 58, 138, 0.15) 40%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
            animation: glowPulse 4s ease-in-out infinite;
            filter: blur(40px);
        }

        @keyframes glowPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 1; }
        }

        /* Phone device frame */
        .phone-mockup {
            width: 300px;
            height: 610px;
            background: linear-gradient(145deg, #1a1a2e 0%, #0a0a1a 100%);
            border-radius: 48px;
            padding: 10px;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.08),
                0 30px 80px rgba(0, 0, 0, 0.5),
                0 0 60px rgba(124, 58, 237, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            position: relative;
            z-index: 2;
            transform: rotateY(-8deg) rotateX(4deg);
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .phone-mockup:hover {
            transform: rotateY(-2deg) rotateX(1deg) translateY(-8px);
        }

        /* Dynamic island notch */
        .phone-notch {
            position: absolute;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 28px;
            background: #000;
            border-radius: 20px;
            z-index: 10;
            box-shadow: 0 0 0 1px rgba(255,255,255,0.04);
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #0D1B2A 0%, #1E1B4B 100%);
            border-radius: 40px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .phone-status-bar {
            padding: 50px 20px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            font-weight: 600;
        }

        .phone-app-header {
            padding: 8px 20px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .phone-app-title {
            font-size: 19px;
            font-weight: 900;
            color: #FFFFFF;
            letter-spacing: -0.01em;
        }

        .phone-live-badge {
            font-size: 10px;
            font-weight: 800;
            color: #A78BFA;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.2);
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            background: #A78BFA;
            border-radius: 50%;
            animation: liveBlink 1.5s ease-in-out infinite;
        }

        @keyframes liveBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Phone SMS card */
        .phone-sms-card {
            margin: 0 14px 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 18px;
            padding: 16px;
            animation: smsSlideIn 0.6s ease-out 1s both;
        }

        @keyframes smsSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .sms-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .sms-sender {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sms-sender-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .sms-sender-name {
            font-size: 13px;
            font-weight: 800;
            color: #FFFFFF;
        }

        .sms-sender-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.4);
        }

        .sms-time {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.35);
        }

        .sms-body {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .sms-body strong {
            color: #FFFFFF;
        }

        .sms-otp-display {
            background: rgba(124, 58, 237, 0.1);
            border: 1.5px solid rgba(74, 222, 128, 0.25);
            border-radius: 14px;
            padding: 14px;
            text-align: center;
        }

        .sms-otp-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(74, 222, 128, 0.6);
            margin-bottom: 4px;
        }

        .sms-otp-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 30px;
            font-weight: 800;
            color: #A78BFA;
            letter-spacing: 0.3em;
            text-shadow: 0 0 20px rgba(74, 222, 128, 0.3);
        }

        .sms-copy-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 11px;
            font-weight: 700;
            color: #A78BFA;
            cursor: pointer;
        }

        /* Second SMS card (stacked, lighter) */
        .phone-sms-card-2 {
            margin: 0 14px 10px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: smsSlideIn 0.6s ease-out 1.4s both;
        }

        .sms2-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4285F4 0%, #0D47A1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sms2-content {
            flex-grow: 1;
            min-width: 0;
        }

        .sms2-title {
            font-size: 12px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.8);
        }

        .sms2-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 15px;
            font-weight: 700;
            color: #818cf8;
            letter-spacing: 0.15em;
        }

        .sms2-time {
            font-size: 9px;
            color: rgba(255, 255, 255, 0.3);
            text-align: right;
        }

        /* Phone bottom bar */
        .phone-bottom-bar {
            margin-top: auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .phone-number-display {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
        }

        .phone-carrier-tag {
            font-size: 9px;
            font-weight: 800;
            color: #A78BFA;
            background: rgba(74, 222, 128, 0.1);
            border: 1px solid rgba(74, 222, 128, 0.2);
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Glassmorphic Floating Service Cards ── */
        .floating-service {
            position: absolute;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            z-index: 3;
            font-size: 13px;
            font-weight: 700;
            color: #FFFFFF;
        }

        .float-service-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #A78BFA;
            box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
            animation: liveBlink 2s ease-in-out infinite;
        }

        .float-1 {
            top: 60px;
            left: -40px;
            animation: floatOrbit1 6s ease-in-out infinite;
        }

        .float-2 {
            top: 200px;
            right: -50px;
            animation: floatOrbit2 7s ease-in-out infinite;
        }

        .float-3 {
            bottom: 180px;
            left: -50px;
            animation: floatOrbit3 8s ease-in-out infinite;
        }

        .float-4 {
            bottom: 60px;
            right: -30px;
            animation: floatOrbit4 6.5s ease-in-out infinite;
        }

        @keyframes floatOrbit1 {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.9; }
            50% { transform: translateY(-16px) translateX(8px); opacity: 1; }
        }

        @keyframes floatOrbit2 {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.85; }
            50% { transform: translateY(-12px) translateX(-10px); opacity: 1; }
        }

        @keyframes floatOrbit3 {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.8; }
            50% { transform: translateY(-18px) translateX(12px); opacity: 1; }
        }

        @keyframes floatOrbit4 {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.9; }
            50% { transform: translateY(-10px) translateX(-8px); opacity: 1; }
        }

        /* ── Hero Stats (Inline in hero) ── */
        .hero-stats-bar {
            position: relative;
            z-index: 2;
            padding: 0 32px 60px;
            max-width: 1280px;
            margin: 0 auto;
        }

        .hero-stats-inner {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .hero-stat-item {
            padding: 28px 24px;
            text-align: center;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: background 0.3s ease;
        }

        .hero-stat-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .hero-stat-num {
            font-size: 32px;
            font-weight: 900;
            color: #FFFFFF;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.02em;
            line-height: 1;
            margin-bottom: 6px;
        }

        .hero-stat-num .stat-accent {
            background: linear-gradient(135deg, #A78BFA, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-stat-label {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* Hero bottom separator gradient line */
        .hero-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(74, 222, 128, 0.3), rgba(129, 140, 248, 0.3), transparent);
        }

        /* ── Live Stats Strip ── */
        .stats-strip {
            background: #FFFFFF;
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
            padding: 24px 0;
        }

        .stats-grid {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            text-align: center;
        }

        .stat-item-num {
            font-size: 32px;
            font-weight: 900;
            color: var(--navy-primary);
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.02em;
        }

        .stat-item-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ── Pricing Section (Real Numbers Specced by User) ── */
        .pricing-section {
            padding: 90px 24px;
            background: #F5F6FA;
            position: relative;
        }

        .section-header {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 50px;
        }

        .section-tag {
            font-size: 12px;
            font-weight: 800;
            color: var(--green-cta);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: rgba(124, 58, 237, 0.1);
            padding: 4px 14px;
            border-radius: var(--radius-pill);
            display: inline-block;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 38px;
            font-weight: 900;
            color: var(--navy-primary);
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }

        .section-desc {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Country Toggle Switcher */
        .country-toggle-box {
            display: inline-flex;
            background: #FFFFFF;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-pill);
            padding: 4px;
            box-shadow: 0 2px 10px rgba(30, 27, 75, 0.04);
            margin: 0 auto 40px;
        }

        .country-toggle-btn {
            background: transparent;
            border: none;
            border-radius: var(--radius-pill);
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 800;
            color: var(--navy-primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .country-toggle-btn.active {
            background: var(--navy-primary);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.25);
        }

        /* 4-Card Pricing Grid */
        .pricing-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            align-items: stretch;
        }

        .pricing-card {
            background: #FFFFFF;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-card);
            padding: 30px 24px;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
            position: relative;
            box-shadow: var(--shadow-soft);
        }

        .pricing-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(30, 27, 75, 0.3);
        }

        .pricing-card.featured {
            border: 2px solid var(--navy-primary);
            box-shadow: 0 16px 48px rgba(30, 27, 75, 0.16);
            position: relative;
        }

        .featured-ribbon {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1E1B4B 0%, #7C3AED 100%);
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 4px 16px;
            border-radius: var(--radius-pill);
            box-shadow: 0 4px 12px rgba(30, 27, 75, 0.25);
            white-space: nowrap;
        }

        .plan-duration {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .plan-price-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 12px;
        }

        .plan-currency {
            font-size: 24px;
            font-weight: 800;
            color: var(--navy-primary);
        }

        .plan-amount {
            font-size: 42px;
            font-weight: 900;
            color: var(--navy-primary);
            font-family: 'JetBrains Mono', monospace;
            line-height: 1;
        }

        .plan-period {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .plan-target {
            font-size: 13px;
            font-weight: 700;
            color: var(--green-cta);
            background: rgba(124, 58, 237, 0.08);
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .plan-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 30px;
            flex-grow: 1;
        }

        .plan-feature-item {
            font-size: 13.5px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-check {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(124, 58, 237, 0.14);
            color: var(--green-cta);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            font-weight: 900;
        }

        .btn-plan-select {
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 800;
            color: var(--navy-primary);
            background: #EEF1F8;
            border: 1.5px solid transparent;
            border-radius: var(--radius-pill);
            padding: 12px 20px;
            text-align: center;
            display: block;
            transition: all 0.2s ease;
        }

        .btn-plan-select:hover {
            background: var(--navy-primary);
            color: #FFFFFF;
        }

        .pricing-card.featured .btn-plan-select {
            background: var(--green-cta);
            color: #FFFFFF;
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);
        }

        .pricing-card.featured .btn-plan-select:hover {
            background: var(--green-hover);
        }

        /* ── Reviews / Testimonials ── */
        .reviews-section {
            padding: 90px 24px;
            background: #FFFFFF;
        }

        .reviews-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .review-card {
            background: #F5F6FA;
            border: 1px solid var(--border-light);
            border-radius: 20px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .review-stars {
            color: #F59E0B;
            display: flex;
            gap: 4px;
            font-size: 16px;
        }

        .review-quote {
            font-size: 14.5px;
            line-height: 1.6;
            color: var(--text-main);
            font-style: italic;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid rgba(30, 27, 75, 0.08);
        }

        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--navy-primary);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
        }

        .author-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--navy-primary);
        }

        .author-location {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── FAQ Accordion Section ── */
        .faq-section {
            padding: 90px 24px;
            background: #F5F6FA;
        }

        .faq-container {
            max-width: 820px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .faq-item {
            background: #FFFFFF;
            border: 1px solid var(--border-light);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(30, 27, 75, 0.03);
            transition: all 0.2s ease;
        }

        .faq-question {
            padding: 20px 24px;
            font-size: 16px;
            font-weight: 800;
            color: var(--navy-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
        }

        .faq-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #EEF1F8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy-primary);
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }

        .faq-answer {
            padding: 0 24px 20px;
            font-size: 14.5px;
            line-height: 1.6;
            color: var(--text-muted);
            display: none;
        }

        .faq-item.open .faq-answer {
            display: block;
        }

        .faq-item.open .faq-icon {
            transform: rotate(180deg);
            background: var(--navy-primary);
            color: #FFFFFF;
        }

        /* ── Tutorial Section ── */
        .tutorial-section {
            padding: 90px 24px;
            background: #FFFFFF;
        }

        .steps-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .step-card {
            background: #F5F6FA;
            border-radius: 20px;
            padding: 36px 28px;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 14px;
            border: 1px solid var(--border-light);
        }

        .step-number {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--navy-primary);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            font-family: 'JetBrains Mono', monospace;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.2);
        }

        .step-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--navy-primary);
        }

        .step-desc {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-muted);
        }

        /* ── Dark Navy Footer ── */
        .main-footer {
            background: var(--navy-dark);
            color: #FFFFFF;
            padding: 70px 24px 30px;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 50px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-brand p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-top: 14px;
            max-width: 320px;
        }

        .footer-heading {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #FFFFFF;
            margin-bottom: 18px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.15s ease;
        }

        .footer-links a:hover {
            color: #FFFFFF;
        }

        .footer-bottom {
            max-width: 1240px;
            margin: 30px auto 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            flex-wrap: wrap;
            gap: 14px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 80px 24px 40px;
            }
            .hero-content { animation-delay: 0s; }
            .hero-title { font-size: 44px; }
            .hero-subtext { margin: 0 auto 12px; }
            .hero-value-strip { justify-content: center; }
            .hero-ctas { justify-content: center; }
            .hero-trust-bar { justify-content: center; flex-wrap: wrap; gap: 16px; }
            .hero-visual { margin-top: 30px; }
            .floating-service { display: none; }
            .phone-mockup { transform: rotateY(0) rotateX(0); }
            .hero-stats-inner { grid-template-columns: repeat(2, 1fr); }
            .hero-stats-bar { padding: 0 24px 40px; }
            .pricing-grid { grid-template-columns: repeat(2, 1fr); }
            .reviews-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .footer-container { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-title { font-size: 34px; }
            .hero-container { padding: 70px 18px 30px; gap: 32px; }
            .hero-value-strip { gap: 12px; }
            .hero-value-item { font-size: 12px; }
            .phone-mockup { width: 260px; height: 530px; }
            .hero-stats-inner { grid-template-columns: repeat(2, 1fr); }
            .hero-stat-num { font-size: 24px; }
            .pricing-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- 1. Sticky Navbar (White bg) -->
    <header class="sticky-navbar" id="navbar">
        <div class="nav-container">
            <!-- Brand Logo Left -->
            <a href="{{ route('landing') }}" class="brand-logo">
                <div class="brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <span class="brand-name">Verixa<span>Text</span></span>
            </a>

            <!-- Nav Links Center -->
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#reviews">Reviews</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#tutorial">Tutorial</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>

            <!-- Auth Actions Right -->
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-nav-login">Log in</a>
                <a href="{{ route('register') }}" class="btn-nav-signup">Sign up</a>
            </div>
        </div>
    </header>

    <!-- 2. Hero Section — World-Class Cinematic Design -->
    <section class="hero-section" id="home">
        <!-- Background layers -->
        <div class="hero-mesh"></div>
        <div class="hero-grid-pattern"></div>
        <div class="hero-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <div class="hero-container">
            <!-- Left Hero Content -->
            <div class="hero-content">
                <div class="hero-badge-row">
                    <span class="hero-badge-pill">Real SIMs</span>
                    <span class="hero-badge-text">Non-VoIP Physical Carrier Network · 200+ Countries</span>
                </div>

                <h1 class="hero-title">
                    Real Non-VoIP Numbers for
                    <span class="hero-title-line2">Instant SMS Verification</span>
                </h1>

                <p class="hero-subtext">
                    Bypass SMS verification blocks on WhatsApp, Telegram, Google, OpenAI, Steam, and banking apps with dedicated physical SIM cards from trusted tier-1 telecom carriers.
                </p>

                <div class="hero-value-strip">
                    <div class="hero-value-item">
                        <span class="hero-value-icon">✓</span>
                        <span>100% Real Carrier SIMs</span>
                    </div>
                    <div class="hero-value-item">
                        <span class="hero-value-icon">⚡</span>
                        <span>Instant Auto-Refund</span>
                    </div>
                    <div class="hero-value-item">
                        <span class="hero-value-icon">🌍</span>
                        <span>200+ Countries</span>
                    </div>
                </div>

                <div class="hero-ctas">
                    <a href="{{ route('register') }}" class="btn-hero-primary">
                        <span>Get Numbers Now</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="#pricing" class="btn-hero-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>View Live Pricing</span>
                    </a>
                </div>

                <div class="hero-trust-bar">
                    <span class="trust-label">Trusted by</span>
                    <div class="trust-logos">
                        <span class="trust-logo-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Resellers
                        </span>
                        <span class="trust-logo-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Developers
                        </span>
                        <span class="trust-logo-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Enterprises
                        </span>
                        <span class="trust-logo-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            Marketers
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Hero: 3D Phone Composition -->
            <div class="hero-visual">
                <div class="phone-glow"></div>

                <!-- Floating Service Icons -->
                <div class="floating-service float-1">
                    <span class="float-service-dot"></span>
                    <span style="font-size: 16px;">💬</span>
                    <span>WhatsApp OTP</span>
                </div>

                <div class="floating-service float-2">
                    <span class="float-service-dot"></span>
                    <span style="font-size: 16px;">🤖</span>
                    <span>OpenAI / ChatGPT</span>
                </div>

                <div class="floating-service float-3">
                    <span class="float-service-dot"></span>
                    <span style="font-size: 16px;">✈️</span>
                    <span>Telegram Code</span>
                </div>

                <div class="floating-service float-4">
                    <span class="float-service-dot"></span>
                    <span style="font-size: 16px;">🔍</span>
                    <span>Google Verify</span>
                </div>

                <!-- 3D Phone Mockup -->
                <div class="phone-mockup">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <div class="phone-status-bar">
                            <span>9:41</span>
                            <span>●●●●●</span>
                        </div>

                        <div class="phone-app-header">
                            <span class="phone-app-title">Messages</span>
                            <span class="phone-live-badge">
                                <span class="live-dot"></span>
                                LIVE
                            </span>
                        </div>

                        <!-- Primary SMS Card -->
                        <div class="phone-sms-card">
                            <div class="sms-header-row">
                                <div class="sms-sender">
                                    <div class="sms-sender-icon">💬</div>
                                    <div>
                                        <div class="sms-sender-name">WhatsApp</div>
                                        <div class="sms-sender-sub">Verification</div>
                                    </div>
                                </div>
                                <span class="sms-time">Just now</span>
                            </div>
                            <p class="sms-body">
                                Your WhatsApp code is <strong>849-201</strong>. Do not share this code with anyone.
                            </p>
                            <div class="sms-otp-display">
                                <div class="sms-otp-label">One-Time Passcode</div>
                                <div class="sms-otp-code">849201</div>
                                <div class="sms-copy-btn">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Copy Code
                                </div>
                            </div>
                        </div>

                        <!-- Secondary SMS Card -->
                        <div class="phone-sms-card-2">
                            <div class="sms2-icon">🔐</div>
                            <div class="sms2-content">
                                <div class="sms2-title">Google Verification</div>
                                <div class="sms2-code">G-482917</div>
                            </div>
                            <div class="sms2-time">2m ago</div>
                        </div>

                        <!-- Phone Bottom Bar -->
                        <div class="phone-bottom-bar">
                            <span class="phone-number-display">🇺🇸 +1 (415) 890-4219</span>
                            <span class="phone-carrier-tag">T-Mobile</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Stats Bar (integrated into hero) -->
        <div class="hero-stats-bar">
            <div class="hero-stats-inner">
                <div class="hero-stat-item">
                    <div class="hero-stat-num"><span class="stat-accent">2.4M+</span></div>
                    <div class="hero-stat-label">SMS Codes Delivered</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num"><span class="stat-accent">200+</span></div>
                    <div class="hero-stat-label">Supported Countries</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num"><span class="stat-accent">99.98%</span></div>
                    <div class="hero-stat-label">Carrier Deliverability</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num"><span class="stat-accent">&lt;3s</span></div>
                    <div class="hero-stat-label">Average Delivery Time</div>
                </div>
            </div>
        </div>

        <div class="hero-divider"></div>
    </section>

    <!-- 4. Pricing Section (Real Numbers Specced by User) -->
    <section class="pricing-section" id="pricing">
        <div class="section-header">
            <span class="section-tag">Transparent Telecom Rates</span>
            <h2 class="section-title">Clean Pricing, No Hidden Fees</h2>
            <p class="section-desc">
                Choose between instant 20-minute short-term lines, private multi-day rentals, or universal All-Services carrier numbers.
            </p>
        </div>

        <!-- Currency & Country Toggle Switchers -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 16px; margin-bottom: 40px;">
            <!-- Currency Switcher (Naira Default) -->
            <div class="country-toggle-box" id="currencyToggleBox" style="margin: 0;">
                <button type="button" class="country-toggle-btn active" id="btnCurrencyNGN" onclick="setPricingCurrency('NGN')">
                    <span>🇳🇬</span>
                    <span>Naira (NGN)</span>
                </button>
                <button type="button" class="country-toggle-btn" id="btnCurrencyUSD" onclick="setPricingCurrency('USD')">
                    <span>💵</span>
                    <span>Dollar (USD)</span>
                </button>
            </div>

            <!-- Country Switcher -->
            <div class="country-toggle-box" id="countryToggleBox" style="margin: 0;">
                <button type="button" class="country-toggle-btn active" id="btnCountryUS" onclick="setPricingCountry('US')">
                    <span>🇺🇸</span>
                    <span>United States Lines</span>
                </button>
                <button type="button" class="country-toggle-btn" id="btnCountryUK" onclick="setPricingCountry('UK')">
                    <span>🇬🇧</span>
                    <span>United Kingdom Lines</span>
                </button>
            </div>
        </div>

        <!-- 4-Card Pricing Grid (Dynamic Currency) -->
        <div class="pricing-grid">
            <!-- 1. 20 min Plan -->
            <div class="pricing-card">
                <div class="plan-duration">20 Minutes</div>
                <div class="plan-price-row">
                    <span class="plan-currency" id="plan1Currency">₦</span>
                    <span class="plan-amount" id="plan1Amount">150</span>
                    <span class="plan-period">/ verification</span>
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #7C3AED; margin-top: -8px; margin-bottom: 12px; font-family: 'JetBrains Mono', monospace;" id="plan1Sub">≈ $0.09 USD</div>
                <div class="plan-target">One website or app</div>
                <ul class="plan-features">
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Full 20 minutes active window</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Instant automated SMS delivery</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Auto-refund if no code arrives</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Tier-1 Carrier SIM (Non-VoIP)</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>High stock available (10,000+)</span></li>
                </ul>
                <a href="{{ route('register') }}" class="btn-plan-select">Select 20 Min</a>
            </div>

            <!-- 2. 3 Days Plan -->
            <div class="pricing-card">
                <div class="plan-duration">3 Days Rental</div>
                <div class="plan-price-row">
                    <span class="plan-currency" id="plan2Currency">₦</span>
                    <span class="plan-amount" id="plan2Amount">1,150</span>
                    <span class="plan-period">/ 3 days</span>
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #7C3AED; margin-top: -8px; margin-bottom: 12px; font-family: 'JetBrains Mono', monospace;" id="plan2Sub">≈ $0.70 USD</div>
                <div class="plan-target">One website or app</div>
                <ul class="plan-features">
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Exclusive dedicated private line</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Unlimited SMS for full 3 days</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Re-verification anytime during rental</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Real physical SIM carrier line</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Auto-renewal supported</span></li>
                </ul>
                <a href="{{ route('register') }}" class="btn-plan-select">Select 3 Days</a>
            </div>

            <!-- 3. 30 Days Plan -->
            <div class="pricing-card">
                <div class="plan-duration">30 Days Monthly</div>
                <div class="plan-price-row">
                    <span class="plan-currency" id="plan3Currency">₦</span>
                    <span class="plan-amount" id="plan3Amount">3,700</span>
                    <span class="plan-period">/ month</span>
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #7C3AED; margin-top: -8px; margin-bottom: 12px; font-family: 'JetBrains Mono', monospace;" id="plan3Sub">≈ $2.25 USD</div>
                <div class="plan-target">One website or app</div>
                <ul class="plan-features">
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Private monthly long-term line</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Unlimited incoming SMS 24/7</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Seamless auto-renewal supported</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Preserve line for continuous use</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Full carrier line ownership</span></li>
                </ul>
                <a href="{{ route('register') }}" class="btn-plan-select">Select 30 Days</a>
            </div>

            <!-- 4. All Services Plan — Featured Best Value -->
            <div class="pricing-card featured">
                <div class="featured-ribbon">★ BEST VALUE · UNIVERSAL LINE</div>
                <div class="plan-duration">All Services (Universal)</div>
                <div class="plan-price-row">
                    <span class="plan-currency" id="allServicesCurrency">₦</span>
                    <span class="plan-amount" id="allServicesPrice">21,450</span>
                    <span class="plan-period" id="allServicesDuration">/ 28-30 days</span>
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #7C3AED; margin-top: -8px; margin-bottom: 12px; font-family: 'JetBrains Mono', monospace;" id="allServicesSub">≈ $12.99 USD</div>
                <div class="plan-target" style="background: rgba(30, 27, 75, 0.08); color: var(--navy-primary);">Any website or app (Universal)</div>
                <ul class="plan-features">
                    <li class="plan-feature-item"><span class="plan-check">✓</span><strong>Works on ANY website or service</strong></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>No need to choose a website</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Unlimited SMS verifications</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span id="allServicesCarrier">T-Mobile (USA) Dedicated Line</span></li>
                    <li class="plan-feature-item"><span class="plan-check">✓</span><span>Auto-renewal supported</span></li>
                </ul>
                <a href="{{ route('register') }}" class="btn-plan-select">Get All Services</a>
            </div>
        </div>
    </section>

    <!-- 5. Testimonials Section -->
    <section class="reviews-section" id="reviews">
        <div class="section-header">
            <span class="section-tag">Verified Customer Reviews</span>
            <h2 class="section-title">Trusted by Developers & Agencies</h2>
            <p class="section-desc">See why over 85,000 businesses and individuals rely on Verixa daily.</p>
        </div>

        <div class="reviews-grid">
            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-quote">"Unlike VoIP numbers that get instantly flagged on WhatsApp and Google, Verixa's physical carrier SIMs worked on the very first try. The 20-minute line is pure gold."</p>
                <div class="review-author">
                    <div class="author-avatar">MR</div>
                    <div>
                        <div class="author-name">Marcus Reid</div>
                        <div class="author-location">United States · Software Engineer</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-quote">"The 30-day All Services line is incredible value. I run multiple automation workflows across Telegram and OpenAI with zero blocks. Deliverability is truly 100%."</p>
                <div class="review-author">
                    <div class="author-avatar">SK</div>
                    <div>
                        <div class="author-name">Sophia Klein</div>
                        <div class="author-location">United Kingdom · Agency Founder</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-quote">"The auto-refund guarantee gives complete peace of mind. If a code doesn't arrive within the timeout, your balance is immediately restored without opening any support ticket."</p>
                <div class="review-author">
                    <div class="author-avatar">DA</div>
                    <div>
                        <div class="author-name">David Adeyemi</div>
                        <div class="author-location">Nigeria · Digital Marketer</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Tutorial Section -->
    <section class="tutorial-section" id="tutorial">
        <div class="section-header">
            <span class="section-tag">Quick Start</span>
            <h2 class="section-title">How It Works in 3 Simple Steps</h2>
            <p class="section-desc">Get verified and bypass SMS roadblocks in less than 60 seconds.</p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <div class="step-title">Select Country & Service</div>
                <p class="step-desc">Pick from over 200 countries and 500+ apps (or select the All-Services universal line).</p>
            </div>

            <div class="step-card">
                <div class="step-number">02</div>
                <div class="step-title">Receive Clean Carrier Number</div>
                <p class="step-desc">Copy your fresh, dedicated non-VoIP telephone line and paste it into your target registration app.</p>
            </div>

            <div class="step-card">
                <div class="step-number">03</div>
                <div class="step-title">Instant SMS Delivery</div>
                <p class="step-desc">Your verification OTP appears instantly on your screen via real-time WebSocket push.</p>
            </div>
        </div>
    </section>

    <!-- 7. FAQ Section (Accordion with Real Site Questions) -->
    <section class="faq-section" id="faq">
        <div class="section-header">
            <span class="section-tag">Frequently Asked Questions</span>
            <h2 class="section-title">Got Questions? We Have Answers.</h2>
            <p class="section-desc">Everything you need to know about our non-VoIP carrier lines and guarantee.</p>
        </div>

        <div class="faq-container">
            <!-- Question 1: Non-VoIP vs VoIP -->
            <div class="faq-item open">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What is the difference between non-VoIP and VoIP numbers?</span>
                    <div class="faq-icon">▼</div>
                </div>
                <div class="faq-answer">
                    VoIP (Voice over IP) numbers are virtual lines generated by internet software (like Google Voice or Skype) and are routinely flagged and rejected by platforms like WhatsApp, Telegram, Google, and banks. Verixa provides 100% genuine non-VoIP physical carrier lines connected directly to cellular towers, ensuring seamless verification acceptance.
                </div>
            </div>

            <!-- Question 2: Carriers -->
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Which mobile carriers do you use?</span>
                    <div class="faq-icon">▼</div>
                </div>
                <div class="faq-answer">
                    We maintain direct interconnects with major tier-1 telecom networks including T-Mobile and AT&T in the United States, EE and Vodafone in the United Kingdom, and corresponding national carriers worldwide.
                </div>
            </div>

            <!-- Question 3: Refund Policy -->
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What happens if I don't receive an SMS code?</span>
                    <div class="faq-icon">▼</div>
                </div>
                <div class="faq-answer">
                    We operate an automatic 100% refund policy. If your short-term (STR) number does not receive an SMS verification code before the countdown timer expires, or if you cancel the active session manually, the entire cost is instantly credited back to your wallet balance.
                </div>
            </div>

            <!-- Question 4: Reclaim / Renew -->
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Can I reclaim or renew my number for long-term use?</span>
                    <div class="faq-icon">▼</div>
                </div>
                <div class="faq-answer">
                    Yes! With our Long-Term Rental (LTR) and All Services plans (3 days, 14 days, 30 days), auto-renewal is fully supported. You can extend your line indefinitely to receive continuous account alerts and future re-verifications.
                </div>
            </div>

            <!-- Question 5: Where Codes Appear -->
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Where do my verification codes appear?</span>
                    <div class="faq-icon">▼</div>
                </div>
                <div class="faq-answer">
                    Your verification codes appear in real time on the live session dashboard and under the "Numbers / SMS" tab via WebSocket. As soon as the carrier tower transmits the SMS, it is rendered on your screen with a 1-click copy button.
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Dark Navy Footer -->
    <footer class="main-footer" id="contact">
        <div class="footer-container">
            <div class="footer-brand">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div class="brand-icon" style="background: rgba(255, 255, 255, 0.15);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <span style="font-size: 20px; font-weight: 900;">Verixa<span style="color: #7C3AED;">Text</span></span>
                </div>
                <p>Enterprise non-VoIP carrier verification infrastructure. Fast, automated, reliable private telephone lines for global digital identity.</p>
                <div style="margin-top: 16px; font-size: 13px; color: rgba(255,255,255,0.7);">
                    Support: <strong style="color: #fff;">support@Verixa.com</strong>
                </div>
            </div>

            <div>
                <div class="footer-heading">Services</div>
                <ul class="footer-links">
                    <li><a href="#pricing">Short-Term Rental (20 min)</a></li>
                    <li><a href="#pricing">Long-Term Rental (30 days)</a></li>
                    <li><a href="#pricing">All Services Universal SIM</a></li>
                    <li><a href="{{ route('login') }}">Developer API</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-heading">Company & Legal</div>
                <ul class="footer-links">
                    <li><a href="{{ route('web.dashboard') }}">Terms of Service</a></li>
                    <li><a href="{{ route('web.dashboard') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('web.dashboard') }}">Auto-Refund Policy</a></li>
                    <li><a href="{{ route('web.dashboard') }}">AML & Compliance</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-heading">Platform</div>
                <ul class="footer-links">
                    <li><a href="{{ route('login') }}">Client Portal</a></li>
                    <li><a href="{{ route('register') }}">Create Account</a></li>
                    <li><a href="{{ route('mobile.home') }}">Mobile App Web Console</a></li>
                    <li><a href="#faq">Network Status</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>© {{ date('Y') }} Verixa Telecom Technologies Ltd. All rights reserved.</div>
            <div style="display: flex; gap: 18px;">
                <span>Security Protected</span>
                <span>Tier-1 Carrier Network</span>
                <span>PCI-DSS Compliant</span>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // Pricing Currency & Country Reactive Controller
        let currentPricingCurrency = 'NGN';
        let currentPricingCountry = 'US';

        const pricingData = {
            NGN: {
                symbol: '₦',
                plan1: { amount: '150', sub: '≈ $0.09 USD' },
                plan2: { amount: '1,150', sub: '≈ $0.70 USD' },
                plan3: { amount: '3,700', sub: '≈ $2.25 USD' },
                allServices: {
                    US: { amount: '21,450', sub: '≈ $12.99 USD' },
                    UK: { amount: '11,500', sub: '≈ $6.99 USD' }
                }
            },
            USD: {
                symbol: '$',
                plan1: { amount: '0.09', sub: '≈ ₦150 NGN' },
                plan2: { amount: '0.70', sub: '≈ ₦1,150 NGN' },
                plan3: { amount: '2.25', sub: '≈ ₦3,700 NGN' },
                allServices: {
                    US: { amount: '12.99', sub: '≈ ₦21,450 NGN' },
                    UK: { amount: '6.99', sub: '≈ ₦11,500 NGN' }
                }
            }
        };

        function updatePricingDisplay() {
            const data = pricingData[currentPricingCurrency];
            const sym = data.symbol;

            // Plan 1 (20m)
            document.getElementById('plan1Currency').textContent = sym;
            document.getElementById('plan1Amount').textContent = data.plan1.amount;
            document.getElementById('plan1Sub').textContent = data.plan1.sub;

            // Plan 2 (3d)
            document.getElementById('plan2Currency').textContent = sym;
            document.getElementById('plan2Amount').textContent = data.plan2.amount;
            document.getElementById('plan2Sub').textContent = data.plan2.sub;

            // Plan 3 (30d)
            document.getElementById('plan3Currency').textContent = sym;
            document.getElementById('plan3Amount').textContent = data.plan3.amount;
            document.getElementById('plan3Sub').textContent = data.plan3.sub;

            // Plan 4 (All Services)
            const allSvc = data.allServices[currentPricingCountry];
            document.getElementById('allServicesCurrency').textContent = sym;
            document.getElementById('allServicesPrice').textContent = allSvc.amount;
            document.getElementById('allServicesSub').textContent = allSvc.sub;

            const allCarrier = document.getElementById('allServicesCarrier');
            if (currentPricingCountry === 'UK') {
                allCarrier.textContent = 'EE (United Kingdom) Dedicated Line';
            } else {
                allCarrier.textContent = 'T-Mobile (USA) Dedicated Line';
            }
        }

        function setPricingCurrency(curr) {
            currentPricingCurrency = curr;
            const btnNGN = document.getElementById('btnCurrencyNGN');
            const btnUSD = document.getElementById('btnCurrencyUSD');
            if (curr === 'NGN') {
                btnNGN.classList.add('active');
                btnUSD.classList.remove('active');
            } else {
                btnUSD.classList.add('active');
                btnNGN.classList.remove('active');
            }
            updatePricingDisplay();
        }

        function setPricingCountry(country) {
            currentPricingCountry = country;
            const btnUS = document.getElementById('btnCountryUS');
            const btnUK = document.getElementById('btnCountryUK');
            if (country === 'UK') {
                btnUK.classList.add('active');
                btnUS.classList.remove('active');
            } else {
                btnUS.classList.add('active');
                btnUK.classList.remove('active');
            }
            updatePricingDisplay();
        }

        // FAQ Accordion Toggle
        function toggleFaq(el) {
            const item = el.parentElement;
            item.classList.toggle('open');
        }
    </script>
</body>
</html>

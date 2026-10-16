<?php

/**
 * Patch NativePHP iOS WKWebView Navigation Policy
 * 
 * This script patches the ContentView.swift file to allow:
 * - All iframe/subframe navigations to stay inside the app
 * - Payment gateway URLs (Stripe, Flutterwave) to open in-app
 * - example.com for testing
 * 
 * This ensures payment checkout stays inside the Jump native app
 * instead of opening Safari.
 */

$filePath = __DIR__ . '/../vendor/nativephp/mobile/resources/xcode/NativePHP/ContentView.swift';

if (!file_exists($filePath)) {
    echo "⚠️ ContentView.swift not found at: {$filePath} (iOS runtime files may be located elsewhere in this NativePHP release)\n";
    exit(0);
}

echo "🔧 Patching NativePHP iOS WKWebView Navigation Policy...\n";

$content = file_get_contents($filePath);

// Check if already patched
if (strpos($content, 'example.com') !== false && strpos($content, '✅ [WKWebView] Allowing IFRAME navigation') !== false) {
    echo "✅ Already patched! No changes needed.\n";
    exit(0);
}

// v4 code pattern
$originalV4 = <<<'SWIFT'
            let scheme = url.scheme?.lowercased() ?? ""

            // Rewrite http(s)://127.0.0.1 to php:// scheme — PHP/Symfony only understands
            // http/https so redirect()->intended() and $request->fullUrl() will always
            // produce http:// URLs for the local server. Route through the scheme handler's
            // redirect path which handles cookie injection from WKHTTPCookieStore.
            //
            // PROTOTYPE: in a Jump WebView session, links the remote app emits point
            // at the dev-server host (Laravel builds absolute URLs from the request
            // Host). Treat that host like 127.0.0.1 — rewrite to php://127.0.0.1 and
            // forward through the scheme handler — so navigations stay in the WebView
            // instead of escaping to Safari. The forward's host:port comes from
            // JumpWebViewSession, so the rewritten URL drops the remote host + port.
            let isJumpHost = JumpWebViewSession.shared.isActive
                && url.host == JumpWebViewSession.shared.host

            if (scheme == "http" || scheme == "https"),
               url.host == "127.0.0.1" || isJumpHost {
                var components = URLComponents(url: url, resolvingAgainstBaseURL: false)
                components?.scheme = "php"
                components?.host = "127.0.0.1"
                components?.port = nil
                if let phpURL = components?.url {
                    NotificationCenter.default.post(
                        name: .redirectToURLNotification,
                        object: nil,
                        userInfo: ["url": phpURL.absoluteString]
                    )
                }
                decisionHandler(.cancel)
                return
            }

            // Open external URLs and system schemes with the system handler
            if ["http", "https", "tel", "mailto", "sms", "facetime", "facetime-audio"].contains(scheme) {
                print("⚠️ [WKWebView] Opening in Safari (external): \(url.absoluteString)")
                UIApplication.shared.open(url)
                decisionHandler(.cancel)
            } else {
                decisionHandler(.allow)
            }
SWIFT;

$patchedV4 = <<<'SWIFT'
            let scheme = url.scheme?.lowercased() ?? ""

            // 1. Allow all subframe/iframe navigations inside WKWebView (e.g. in-app checkout iframe modal)
            // CRITICAL: iframes must NEVER open Safari - they stay inside the app
            if navigationAction.targetFrame?.isMainFrame == false {
                print("✅ [WKWebView] Allowing IFRAME navigation: \(url.absoluteString)")
                decisionHandler(.allow)
                return
            }

            // 2. Handle popups / new windows (e.g. target="_blank" or window.open in payment gateway)
            // Keep them inside the app rather than escaping to Safari
            let host = url.host?.lowercased() ?? ""
            let isPaymentHost = host.contains("paystack.com") || host.contains("paystack.co") ||
                               host.contains("stripe.com") || host.contains("flutterwave.com") ||
                               host.contains("interswitchng.com") || host.contains("nibss-plc") ||
                               host.contains("cardinalcommerce") || host.contains("arcot.com") ||
                               host.contains("modirum.com") || host.contains("verifiedbyvisa") ||
                               host.contains("mastercard")

            if navigationAction.targetFrame == nil {
                print("✅ [WKWebView] Subframe/popup requested new window - keeping in webview: \(url.absoluteString)")
                webView.load(navigationAction.request)
                decisionHandler(.cancel)
                return
            }

            // Rewrite http(s)://127.0.0.1 to php:// scheme — PHP/Symfony only understands
            // http/https so redirect()->intended() and $request->fullUrl() will always
            // produce http:// URLs for the local server. Route through the scheme handler's
            // redirect path which handles cookie injection from WKHTTPCookieStore.
            //
            // PROTOTYPE: in a Jump WebView session, links the remote app emits point
            // at the dev-server host (Laravel builds absolute URLs from the request
            // Host). Treat that host like 127.0.0.1 — rewrite to php://127.0.0.1 and
            // forward through the scheme handler — so navigations stay in the WebView
            // instead of escaping to Safari. The forward's host:port comes from
            // JumpWebViewSession, so the rewritten URL drops the remote host + port.
            let isJumpHost = JumpWebViewSession.shared.isActive
                && url.host == JumpWebViewSession.shared.host

            if (scheme == "http" || scheme == "https"),
               url.host == "127.0.0.1" || isJumpHost {
                var components = URLComponents(url: url, resolvingAgainstBaseURL: false)
                components?.scheme = "php"
                components?.host = "127.0.0.1"
                components?.port = nil
                if let phpURL = components?.url {
                    NotificationCenter.default.post(
                        name: .redirectToURLNotification,
                        object: nil,
                        userInfo: ["url": phpURL.absoluteString]
                    )
                }
                decisionHandler(.cancel)
                return
            }

            // 3. Allow internal local/LAN hosts and payment gateway hosts in main frame
            if isPaymentHost ||
               host == "127.0.0.1" || host == "localhost" ||
               host.hasPrefix("192.168.") || host.hasPrefix("172.") || host.hasPrefix("10.") {
                print("✅ [WKWebView] Allowing MAIN FRAME navigation: \(url.absoluteString)")
                decisionHandler(.allow)
                return
            }

            // Open system schemes with the system handler (tel, sms, mailto)
            if ["tel", "mailto", "sms", "facetime", "facetime-audio"].contains(scheme) {
                print("📱 [WKWebView] Opening system handler: \(url.absoluteString)")
                UIApplication.shared.open(url)
                decisionHandler(.cancel)
                return
            }

            // All web (http / https) navigations stay strictly in-app (Never open Safari)
            if scheme == "http" || scheme == "https" {
                print("🌐 [WKWebView] Keeping navigation in-app: \(url.absoluteString)")
                decisionHandler(.allow)
                return
            }

            decisionHandler(.allow)
SWIFT;

// v3 code pattern
$originalV3 = <<<'SWIFT'
            // 1. Allow all subframe/iframe navigations inside WKWebView (e.g. in-app checkout iframe modal)
            if navigationAction.targetFrame?.isMainFrame == false {
                decisionHandler(.allow)
                return
            }

            // 2. Rewrite http(s)://127.0.0.1 to php:// scheme — PHP/Symfony only understands
            // http/https so redirect()->intended() and $request->fullUrl() will always
            // produce http:// URLs for the local server. Route through the scheme handler's
            // redirect path which handles cookie injection from WKHTTPCookieStore.
            if (scheme == "http" || scheme == "https"),
               url.host == "127.0.0.1" {
                var components = URLComponents(url: url, resolvingAgainstBaseURL: false)
                components?.scheme = "php"
                if let phpURL = components?.url {
                    NotificationCenter.default.post(
                        name: .redirectToURLNotification,
                        object: nil,
                        userInfo: ["url": phpURL.absoluteString]
                    )
                }
                decisionHandler(.cancel)
                return
            }

            // 3. Allow internal local/LAN hosts and payment gateway hosts in main frame
            let host = url.host?.lowercased() ?? ""
            if host.contains("stripe.com") || host.contains("flutterwave.com") ||
               host == "127.0.0.1" || host == "localhost" ||
               host.hasPrefix("192.168.") || host.hasPrefix("172.") || host.hasPrefix("10.") {
                decisionHandler(.allow)
                return
            }

            // 4. Open external non-app URLs and system schemes with the system handler
            if ["http", "https", "tel", "mailto", "sms", "facetime", "facetime-audio"].contains(scheme) {
                UIApplication.shared.open(url)
                decisionHandler(.cancel)
            } else {
                decisionHandler(.allow)
            }
SWIFT;

$patchedContent = $content;
if (strpos($content, $originalV4) !== false) {
    $patchedContent = str_replace($originalV4, $patchedV4, $content);
} elseif (strpos($content, $originalV3) !== false) {
    $patchedContent = str_replace($originalV3, $patchedV4, $content);
}

if ($patchedContent !== $content) {
    file_put_contents($filePath, $patchedContent);
    echo "✅ Successfully patched ContentView.swift!\n";
    echo "✅ Payment URLs will now stay inside the Jump app.\n";
} else {
    echo "ℹ️ ContentView.swift already up to date.\n";
}

// -------------------------------------------------------------
// Patch Jump Server (http-server.php, router.php, JumpCommand.php)
// -------------------------------------------------------------
$httpServerPath = __DIR__ . '/../vendor/nativephp/mobile/resources/jump/http-server.php';
if (file_exists($httpServerPath)) {
    $httpContent = file_get_contents($httpServerPath);
    $modified = false;

    if (strpos($httpContent, "'startUrl'") === false) {
        $target = "'appName' => getenv('APP_NAME') ?: 'Laravel',";
        $replacement = "'appName' => getenv('APP_NAME') ?: 'Laravel',\n    'appUi' => getenv('JUMP_APP_UI') ?: 'webview',\n    'startUrl' => getenv('JUMP_START_URL') ?: '/',";
        $httpContent = str_replace($target, $replacement, $httpContent);
        $modified = true;
    }

    if (strpos($httpContent, "'start_url' => \$JUMP['startUrl']") === false) {
        $target = "'ui' => getenv('JUMP_APP_UI') ?: 'native-ui',";
        $replacement = "'ui' => \$JUMP['appUi'],\n            'start_url' => \$JUMP['startUrl'],\n            'url' => \"http://{\$JUMP['displayHost']}:{\$JUMP['httpPort']}{\$JUMP['startUrl']}\",";
        $httpContent = str_replace($target, $replacement, $httpContent);
        $modified = true;
    }

    if (strpos($httpContent, "if (\$path === '/jump/open')") === false) {
        $openRoute = <<<'PHP'

    // Handle deep link redirect — camera scans HTTP URL, browser redirects to jump:// app
    if ($path === '/jump/open') {
        $deepLink = "jump://connect?host={$JUMP['displayHost']}&port={$JUMP['httpPort']}";
        $appName = htmlspecialchars($JUMP['appName']);
        $html = <<<HTML
<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Opening {$appName} in Jump...</title>
<script>window.location.href = "{$deepLink}";</script>
</head><body style="font-family:system-ui;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#111;color:#fff;text-align:center">
<div>
<h2>Opening in Jump...</h2>
<p style="color:#888">If the app doesn't open, <a href="{$deepLink}" style="color:#8B5CF6">tap here</a>.</p>
<p style="color:#666;font-size:14px;margin-top:24px">Don't have Jump? <a href="https://apps.apple.com/app/jump/id6738194400" style="color:#8B5CF6">Download for iOS</a></p>
</div>
</body></html>
HTML;
        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $html);
    }
PHP;
        $target = "jumpRouterLog(\$method.' '.\$uri.' [200]');\n\n        return new Response(200, ['Content-Type' => 'application/json'], json_encode(\$info));\n    }";
        $replacement = $target . "\n" . $openRoute;
        $httpContent = str_replace($target, $replacement, $httpContent);
        $modified = true;
    }

    if ($modified) {
        file_put_contents($httpServerPath, $httpContent);
        echo "✅ Patched http-server.php for Jump deep links & webview support.\n";
    }
}

$jumpCmdPath = __DIR__ . '/../vendor/nativephp/mobile/src/Commands/JumpCommand.php';
if (file_exists($jumpCmdPath)) {
    $jumpCmd = file_get_contents($jumpCmdPath);
    $cmdModified = false;

    if (strpos($jumpCmd, "'JUMP_START_URL'") === false) {
        $target = "'JUMP_APP_UI' => \$appUi,";
        $replacement = "'JUMP_APP_UI' => \$appUi,\n            'JUMP_START_URL' => \$startUrl,";
        $jumpCmd = str_replace($target, $replacement, $jumpCmd);
        $cmdModified = true;
    }

    if (strpos($jumpCmd, "if (\$port <= 0 || PHP_OS_FAMILY === 'Windows')") === false) {
        $target = "private function killListenersOnPort(int \$port): void\n    {\n        if (\$port <= 0) {";
        $replacement = "private function killListenersOnPort(int \$port): void\n    {\n        if (\$port <= 0 || PHP_OS_FAMILY === 'Windows') {";
        $jumpCmd = str_replace($target, $replacement, $jumpCmd);
        $cmdModified = true;
    }

    if ($cmdModified) {
        file_put_contents($jumpCmdPath, $jumpCmd);
        echo "✅ Patched JumpCommand.php for Windows process handling & start URL.\n";
    }
}

echo "✅ All NativePHP patches verified.\n";
exit(0);

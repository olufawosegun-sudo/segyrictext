<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectPlatform
{
    /**
     * Handle an incoming request.
     *
     * Detects whether the request originates from:
     * - 'android' (NativePHP Android runtime)
     * - 'ios' (NativePHP iOS runtime)
     * - 'mobile_browser' (Mobile web browser)
     * - 'web' (Desktop / standard web browser)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $platform = 'web';
        $userAgent = $request->header('User-Agent', '');
        $clientHeader = $request->header('X-NativePHP-Platform') ?? $request->header('X-Platform');
        $isJumpOrNativeBridge = getenv('JUMP_BRIDGE_PORT') !== false
            || $request->hasHeader('X-NativePHP')
            || in_array($request->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array($request->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);

        if ($clientHeader) {
            $platform = strtolower($clientHeader);
        } elseif (str_contains($userAgent, 'NativePHP-Android') || str_contains($userAgent, 'NativeAndroid')) {
            $platform = 'android';
        } elseif (str_contains($userAgent, 'NativePHP-iOS') || str_contains($userAgent, 'NativeiOS')) {
            $platform = 'ios';
        } elseif (preg_match('/(iPhone|iPod|iPad|Android|BlackBerry|IEMobile|Opera Mini)/i', $userAgent)) {
            if ($isJumpOrNativeBridge) {
                $platform = preg_match('/(Android)/i', $userAgent) ? 'android' : 'ios';
            } else {
                $platform = 'mobile_browser';
            }
        } elseif ($isJumpOrNativeBridge) {
            $platform = 'android'; // Default NativePHP mobile runtime
        }

        $isNative = in_array($platform, ['android', 'ios']) || $isJumpOrNativeBridge;

        $request->attributes->set('platform', $platform);
        $request->attributes->set('is_native', $isNative);

        // Secure authentication bridge for WKWebView and mobile Jump where cookies may be delayed
        if (!\Illuminate\Support\Facades\Auth::check() && $request->filled('auth_token')) {
            try {
                $payload = json_decode(\Illuminate\Support\Facades\Crypt::decryptString($request->query('auth_token')), true);
                if (isset($payload['user_id'], $payload['expires_at']) && $payload['expires_at'] >= time()) {
                    \Illuminate\Support\Facades\Auth::loginUsingId($payload['user_id'], true);
                    if ($request->hasSession()) {
                        $request->session()->regenerate();
                    }
                }
            } catch (\Throwable $e) {
                // Invalid or expired token; continue request normally
            }
        }

        // Share with all Blade views
        view()->share('currentPlatform', $platform);
        view()->share('isNativePlatform', in_array($platform, ['android', 'ios']));

        $response = $next($request);
        $response->headers->set('X-Detected-Platform', $platform);

        return $response;
    }
}

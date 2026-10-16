<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenBridge
{
    /**
     * Handle incoming request and authenticate via auth_token fallback if session cookie was delayed/dropped.
     * Crucial for NativePHP Android WebView / Jump reverse proxies.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('auth_token')) {
            try {
                $payload = json_decode(Crypt::decryptString($request->query('auth_token')), true);
                if (isset($payload['user_id'], $payload['expires_at']) && $payload['expires_at'] >= time()) {
                    if (!Auth::check() || Auth::id() !== (int) $payload['user_id']) {
                        Auth::loginUsingId($payload['user_id'], true);
                    }
                    if ($request->hasSession()) {
                        $request->session()->regenerate();

                        // Re-set just_logged_in after regeneration (regeneration wipes session data).
                        // This flag is used by mobile.blade.php to inject a sessionStorage flag
                        // that suppresses auto-recovery of pending payments after login.
                        $justLoggedIn = $payload['just_logged_in'] ?? time();
                        $request->session()->put('just_logged_in', $justLoggedIn);
                    }
                    Log::info('[AUTH-TOKEN-BRIDGE] Successfully authenticated user via token', [
                        'user_id' => $payload['user_id'],
                        'path' => $request->path(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('[AUTH-TOKEN-BRIDGE] Token authentication failed: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}

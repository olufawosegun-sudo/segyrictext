<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Whitelist routes to prevent infinite redirection loops
        if ($request->routeIs('profile.complete', 'profile.complete.store', 'logout')) {
            return $next($request);
        }

        // Check if user profile is missing any mandatory field
        if (!$user->isProfileComplete()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your profile is incomplete. Please complete your profile to continue.',
                    'profile_complete_required' => true,
                    'redirect' => route('profile.complete'),
                ], 403);
            }

            return redirect()->route('profile.complete');
        }

        return $next($request);
    }
}

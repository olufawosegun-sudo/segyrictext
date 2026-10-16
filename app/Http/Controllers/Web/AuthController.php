<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        $request->session()->forget([
            'url.intended',
            'intended',
            'verixa_pending_payment',
            'Verixa_pending_payment',
            'segyric_pending_payment',
            'pending_deposit_ref',
            'pending_deposit_amt',
            'pending_deposit_time',
            'checkout_url',
            'payment_url',
            'paystack_url',
        ]);
        $request->session()->pull('url.intended');

        // Detect mobile platform and serve appropriate view
        $platform = $request->attributes->get('platform', 'web');
        $userAgent = $request->header('User-Agent', '');
        $isMobile = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || $request->attributes->get('is_native', false)
            || preg_match('/(iPhone|iPod|iPad|Android|Mobile)/i', $userAgent);

        if (Auth::check()) {
            if ($isMobile) {
                return redirect()->to(url('/mobile'));
            }
            return redirect()->to(route('web.dashboard'));
        }
        
        return view($isMobile ? 'native.auth.login' : 'web.auth.login');
    }

    public function login(Request $request): RedirectResponse|Response|View
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Detect native/mobile context — always respond with JSON for these
        $platform = $request->attributes->get('platform', 'web');
        $userAgent = $request->header('User-Agent', '');
        $isNative = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || $request->attributes->get('is_native', false)
            || preg_match('/(iPhone|iPod|iPad|Android|Mobile)/i', $userAgent)
            || $request->hasHeader('X-NativePHP')
            || $request->hasHeader('X-NativePHP-Platform')
            || str_contains($userAgent, 'NativePHP')
            || getenv('JUMP_BRIDGE_PORT') !== false
            || in_array($request->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array($request->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);
        $wantsJson = $request->expectsJson() || $request->ajax() || $request->isJson();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->forget([
                'url.intended',
                'intended',
                'verixa_pending_payment',
                'Verixa_pending_payment',
                'segyric_pending_payment',
                'pending_deposit_ref',
                'pending_deposit_amt',
                'pending_deposit_time',
                'checkout_url',
                'payment_url',
                'paystack_url',
            ]);
            $request->session()->pull('url.intended');

            $request->session()->put('just_logged_in', time());
            $user = Auth::user();
            $destination = $this->getDestinationUrl($request, $user);

            \Illuminate\Support\Facades\Log::info('[LOGIN-DEBUG] About to return response', [
                'user_id' => $user->id,
                'email' => $user->email,
                'platform' => $platform,
                'destination' => $destination,
                'is_native' => $isNative,
                'wantsJson' => $wantsJson,
                'will_return' => $wantsJson ? 'JSON' : 'REDIRECT',
            ]);

            // AJAX / JSON requests (including in-app mobile fetch): Return JSON response
            if ($wantsJson) {
                \Illuminate\Support\Facades\Log::info('[LOGIN-DEBUG] Returning JSON response', ['redirect' => $destination]);
                return response()->json([
                    'success' => true,
                    'redirect' => $destination,
                    'message' => 'Login successful.',
                ]);
            }

            // Standard web / form submission: Redirect to destination (never return view directly on POST)
            \Illuminate\Support\Facades\Log::info('[LOGIN-DEBUG] Returning redirect', ['to' => $destination]);
            return redirect()->to($destination);
        }

        if ($wantsJson) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request): View|RedirectResponse|Response
    {
        $request->session()->forget(['url.intended', 'verixa_pending_payment']);

        // Detect mobile platform and serve appropriate view
        $platform = $request->attributes->get('platform', 'web');
        $userAgent = $request->header('User-Agent', '');
        $isMobile = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || $request->attributes->get('is_native', false)
            || preg_match('/(iPhone|iPod|iPad|Android|Mobile)/i', $userAgent);

        if (Auth::check()) {
            if ($isMobile) {
                return app(\App\Http\Controllers\Mobile\MobileAppController::class)->home();
            }
            return redirect()->to(route('web.dashboard'));
        }
        
        return view($isMobile ? 'native.auth.register' : 'web.auth.register');
    }

    public function register(Request $request): RedirectResponse|Response|View
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country_code' => ['nullable', 'string', 'max:3'],
            'currency_code' => ['nullable', 'string', 'max:3'],
        ], [
            'phone.regex' => 'Please enter a valid phone number (e.g. +1234567890).',
            'phone.unique' => 'This phone number is already registered to another account.',
        ]);

        $countryCode = strtoupper($validated['country_code'] ?? $request->header('CF-IPCountry') ?? $request->header('X-Country-Code') ?? 'NG');
        $currencyCode = !empty($validated['currency_code']) ? strtoupper($validated['currency_code']) : null;

        $fullName = trim("{$validated['first_name']} " . (!empty($validated['middle_name']) ? "{$validated['middle_name']} " : '') . $validated['last_name']);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country_code' => $countryCode,
            'currency_code' => $currencyCode,
        ]);

        // Initialize user wallet in their signup country's currency
        app(\App\Application\Services\WalletService::class)->getOrCreateUserWallet($user);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget(['url.intended', 'verixa_pending_payment']);

            $request->session()->flash('just_logged_in', time());
        $destination = $this->getDestinationUrl($request, $user);

        // Detect native/mobile context
        $platform = $request->attributes->get('platform', 'web');
        $isNative = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || $request->attributes->get('is_native', false)
            || $request->hasHeader('X-NativePHP')
            || $request->hasHeader('X-NativePHP-Platform')
            || str_contains($request->header('User-Agent', ''), 'NativePHP')
            || getenv('JUMP_BRIDGE_PORT') !== false
            || in_array($request->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array($request->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);
        $wantsJson = $request->expectsJson() || $request->ajax() || $request->isJson();

        // AJAX / JSON requests (including in-app mobile fetch): Return JSON response
        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'redirect' => $destination,
                'message' => 'Account created successfully.',
            ]);
        }

        // Standard web / form submission: Redirect to destination (never return view directly on POST)
        return redirect()->to($destination);
    }

    /**
     * Resolve destination URL after login.
     * Generates an encrypted auth_token for mobile / NativePHP environments to bridge
     * session cookies across 302 redirects in WKWebView and reverse proxies.
     */
    protected function getDestinationUrl(Request $request, ?User $user = null): string
    {
        $platform = $request->attributes->get('platform', 'web');
        $isNative = $request->attributes->get('is_native', false);
        $userAgent = $request->header('User-Agent', '');

        $isNativeOrMobile = in_array($platform, ['android', 'ios', 'mobile_browser'])
            || $isNative
            || $request->hasHeader('X-NativePHP')
            || $request->hasHeader('X-NativePHP-Platform')
            || $request->hasHeader('X-Platform')
            || str_contains($userAgent, 'NativePHP')
            || str_contains($userAgent, 'Android')
            || str_contains($userAgent, 'iPhone')
            || str_contains($userAgent, 'iPad')
            || str_contains($userAgent, 'Mobile')
            || getenv('JUMP_BRIDGE_PORT') !== false
            || in_array($request->getPort(), [3000, 3001, 3002, 3003, 3004])
            || in_array($request->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);

        // Always go to dashboard/home after login - use direct URLs for speed
        return $isNativeOrMobile ? url('/mobile') : url('/dashboard');
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $source = $request->input('source');
        $platform = $request->input('platform') ?? $request->attributes->get('platform', 'web');
        $isNative = in_array($platform, ['android', 'ios']);

        $destination = ($source === 'desktop' || ! $isNative)
            ? route('landing')
            : route('login');

        if ($request->expectsJson() || $request->ajax() || $request->isJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $destination,
                'message' => 'Logged out successfully.'
            ]);
        }

        return redirect()->to($destination)->with('success', 'Logged out successfully.');
    }
}

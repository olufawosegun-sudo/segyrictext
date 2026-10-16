<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompletionController extends Controller
{
    /**
     * Show the complete profile form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // If profile is already complete, redirect to destination
        if ($user->isProfileComplete()) {
            return redirect()->to($this->getDestinationUrl($request));
        }

        // Intelligently pre-populate first and last name from legacy 'name' if not set
        $firstName = $user->first_name;
        $middleName = $user->middle_name;
        $lastName = $user->last_name;

        if (empty($firstName) && !empty($user->name)) {
            $nameParts = preg_split('/\s+/', trim($user->name));
            if (count($nameParts) === 1) {
                $firstName = $nameParts[0];
            } elseif (count($nameParts) === 2) {
                $firstName = $nameParts[0];
                $lastName = $nameParts[1];
            } elseif (count($nameParts) >= 3) {
                $firstName = $nameParts[0];
                $middleName = $nameParts[1];
                $lastName = implode(' ', array_slice($nameParts, 2));
            }
        }

        return view('web.auth.complete-profile', [
            'user' => $user,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'phone' => $user->phone,
        ]);
    }

    /**
     * Save the completed profile data.
     */
    public function store(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:32',
                Rule::unique('users', 'phone')->ignore($user->id),
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
            ],
        ], [
            'phone.regex' => 'Please enter a valid phone number with optional country code (e.g. +1234567890).',
            'phone.unique' => 'This phone number is already registered to another account.',
        ]);

        $fullName = trim("{$validated['first_name']} " . (!empty($validated['middle_name']) ? "{$validated['middle_name']} " : '') . $validated['last_name']);

        $user->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'name' => $fullName,
        ]);

        $destination = $this->getDestinationUrl($request);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile completed successfully.',
                'redirect' => $destination,
            ]);
        }

        return redirect()->to($destination)->with('success', 'Your profile has been completed!');
    }

    /**
     * Resolve destination URL after profile completion.
     */
    protected function getDestinationUrl(Request $request): string
    {
        $platform = $request->attributes->get('platform', 'web');
        $isMobile = in_array($platform, ['android', 'ios', 'mobile_browser']);

        if ($isMobile) {
            $dest = \Illuminate\Support\Facades\Route::has('mobile.home')
                ? route('mobile.home')
                : url('/home');
        } elseif (\Illuminate\Support\Facades\Route::has('web.dashboard')) {
            $dest = route('web.dashboard');
        } else {
            $dest = url('/home');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            $isNativeOrMobile = in_array($platform, ['android', 'ios', 'mobile_browser'])
                || $request->attributes->get('is_native', false)
                || $request->hasHeader('X-NativePHP')
                || getenv('JUMP_BRIDGE_PORT') !== false
                || in_array($request->getPort(), [3000, 3001, 3002, 3003, 3004]);

            if ($isNativeOrMobile) {
                $authToken = \Illuminate\Support\Facades\Crypt::encryptString(json_encode([
                    'user_id' => $user->id,
                    'expires_at' => time() + 300,
                ]));
                $separator = str_contains($dest, '?') ? '&' : '?';
                return $dest . $separator . 'auth_token=' . urlencode($authToken);
            }
        }

        return $dest;
    }
}

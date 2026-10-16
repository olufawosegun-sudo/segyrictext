<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate and generate Sanctum token for mobile app.
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $deviceName = $request->input('device_name', $request->header('X-Platform', 'Mobile App'));
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Authenticated successfully',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Register a new user and issue token.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string', 'max:3'],
            'currency_code' => ['nullable', 'string', 'max:3'],
        ]);

        $countryCode = strtoupper($validated['country_code'] ?? $request->header('CF-IPCountry') ?? $request->header('X-Country-Code') ?? 'NG');
        $currencyCode = !empty($validated['currency_code']) ? strtoupper($validated['currency_code']) : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country_code' => $countryCode,
            'currency_code' => $currencyCode,
        ]);

        // Initialize user wallet in signup country currency
        $wallet = app(\App\Application\Services\WalletService::class)->getOrCreateUserWallet($user);

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully',
            'token' => $token,
            'user' => $user->load('primaryWallet.currency'),
        ], 201);
    }

    /**
     * Return authenticated user profile and stats.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'stats' => [
                'total_documents' => $user->documents()->count(),
                'processed_documents' => $user->documents()->where('status', 'processed')->count(),
                'categories' => $user->categories()->count(),
            ],
        ]);
    }

    /**
     * Send password reset link to user email (API).
     */
    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'If that email is registered, a password reset link has been sent.',
        ]);
    }

    /**
     * Reset user password using token (API).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successfully. You can now log in with your new password.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __($status),
        ], 400);
    }

    /**
     * Revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}

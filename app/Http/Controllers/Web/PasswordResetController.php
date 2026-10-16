<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot-password form.
     */
    public function showForgotForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->to(route('web.dashboard'));
        }
        return view('web.auth.forgot-password');
    }

    /**
     * Send password reset link via email.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'We\'ve sent a password reset link to your email address. Check your inbox!');
        }

        // Even if email not found, show generic success for security (prevents user enumeration)
        return back()->with('success', 'If that email is registered, you\'ll receive a reset link shortly.');
    }

    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('web.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Process password reset.
     */
    public function resetPassword(Request $request): RedirectResponse
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
            return redirect()
                ->route('login')
                ->with('success', '✓ Password reset successfully! You can now sign in with your new password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}

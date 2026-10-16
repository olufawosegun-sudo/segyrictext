<?php

use App\Http\Controllers\Mobile\MobileAppController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DocumentController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\ProfileCompletionController;
use App\Http\Controllers\Web\SchedulerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Root Route: Platform-aware entry point
// Mobile/Native → Splash (always on mobile/native) → Login/Home | Desktop Web → Landing Page / Dashboard
Route::get('/', function () {
    $platform = request()->attributes->get('platform', 'web');
    $isNative = request()->attributes->get('is_native', false);
    $userAgent = request()->header('User-Agent', '');

    $isMobileOrNative = $isNative 
        || in_array($platform, ['android', 'ios', 'mobile_browser'])
        || request()->header('X-NativePHP-Platform')
        || request()->header('X-Platform')
        || str_contains($userAgent, 'NativePHP')
        || str_contains($userAgent, 'Android')
        || str_contains($userAgent, 'iPhone')
        || str_contains($userAgent, 'iPad')
        || str_contains($userAgent, 'Mobile')
        || getenv('JUMP_BRIDGE_PORT') !== false
        || in_array(request()->getPort(), [3000, 3001, 3002, 3003, 3004])
        || in_array(request()->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004])
        || request()->hasHeader('X-Forwarded-Host');

    // Mobile / Native Application runtime → Splash screen or direct home
    if ($isMobileOrNative) {
        if (auth()->check()) {
            return app(\App\Http\Controllers\Mobile\MobileAppController::class)->home();
        }
        return view('splash');
    }

    // Standard Desktop Web Browser:
    if (auth()->check()) {
        return redirect()->to(route('web.dashboard'));
    }

    // Web Desktop Marketing Landing Page
    return view('web.landing');
})->name('splash');

// Explicit Splash Direct Route
Route::get('/splash', function () {
    return view('splash');
})->name('splash.direct');

// Desktop Web Marketing Landing Page
Route::get('/landing', function () {
    return view('web.landing');
})->name('landing');

// Universal /home entry point (handles post-payment callbacks & platform routing)
Route::get('/home', function () {
    $queryString = request()->getQueryString();
    $suffix = $queryString ? ('?' . $queryString) : '';

    $platform = request()->attributes->get('platform', 'web');
    $userAgent = request()->header('User-Agent', '');
    $isMobileOrNative = in_array($platform, ['android', 'ios', 'mobile_browser'])
        || request()->attributes->get('is_native', false)
        || request()->hasHeader('X-NativePHP')
        || request()->hasHeader('X-NativePHP-Platform')
        || request()->hasHeader('X-Platform')
        || str_contains($userAgent, 'NativePHP')
        || str_contains($userAgent, 'CFNetwork')
        || str_contains($userAgent, 'iPhone')
        || str_contains($userAgent, 'iPad')
        || str_contains($userAgent, 'Android')
        || str_contains($userAgent, 'Mobile')
        || getenv('JUMP_BRIDGE_PORT') !== false
        || in_array(request()->getPort(), [3000, 3001, 3002, 3003, 3004])
        || in_array(request()->header('X-Forwarded-Port'), [3000, 3001, 3002, 3003, 3004]);

    if (auth()->check()) {
        if ($isMobileOrNative) {
            return app(\App\Http\Controllers\Mobile\MobileAppController::class)->home();
        }
        return redirect()->to(route('web.dashboard') . $suffix);
    }
    return redirect()->to(route('login') . $suffix);
})->name('home');

// Profile Completion Routes (Authenticated, exempt from profile.complete middleware)
Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', [ProfileCompletionController::class, 'show'])->name('profile.complete');
    Route::post('/complete-profile', [ProfileCompletionController::class, 'store'])->name('profile.complete.store');
});

// Protected Desktop Web Routes
Route::middleware(['auth', 'profile.complete'])->name('web.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Documents Management
    Route::resource('documents', DocumentController::class);
    Route::post('/documents/{id}/process', [DocumentController::class, 'process'])->name('documents.process');

    // Scheduler & Cron Manager
    Route::get('/scheduler', [SchedulerController::class, 'index'])->name('scheduler.index');
    Route::post('/scheduler/trigger', [SchedulerController::class, 'trigger'])->name('scheduler.trigger');
    Route::post('/scheduler/clear-logs', [SchedulerController::class, 'clearLogs'])->name('scheduler.clear-logs');

    // Sidebar Console Modules
    Route::get('/str-numbers', [DashboardController::class, 'strNumbers'])->name('str-numbers');
    Route::get('/ltr-numbers', [DashboardController::class, 'ltrNumbers'])->name('ltr-numbers');
    Route::get('/sms-logs', [DashboardController::class, 'smsLogs'])->name('sms-logs');
    Route::get('/support', [DashboardController::class, 'support'])->name('support');
    Route::get('/account', [DashboardController::class, 'account'])->name('account');
    Route::get('/pricing', [DashboardController::class, 'pricing'])->name('pricing');
    Route::get('/refill-credits', [DashboardController::class, 'refillCredits'])->name('refill-credits');
});

// Protected Mobile Dedicated Routes (Supports NativePHP Mobile and Mobile Browser)
Route::middleware(['auth', 'profile.complete'])->prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/', [MobileAppController::class, 'home'])->name('home');
    Route::get('/get-number', [MobileAppController::class, 'getNumber'])->name('getNumber');
    Route::get('/sessions', [MobileAppController::class, 'sessions'])->name('sessions');
    Route::get('/wallet', [MobileAppController::class, 'wallet'])->name('wallet');
    Route::get('/deposit', [MobileAppController::class, 'deposit'])->name('deposit');
    Route::get('/settings', [MobileAppController::class, 'settings'])->name('settings');
});

// Top-level /deposit route
Route::middleware(['auth', 'profile.complete'])->get('/deposit', [MobileAppController::class, 'deposit'])->name('deposit');

Route::middleware(['auth', 'profile.complete'])->prefix('api/payment')->group(function () {
    Route::post('/initialize', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'max:3'],
            'gateway' => ['nullable', 'string'],
        ]);

        $user = auth()->user();
        $paymentService = app(\App\Application\Services\PaymentService::class);

        try {
            $result = $paymentService->initiatePayment(
                user: $user,
                amount: (float) $request->input('amount'),
                currency: $request->input('currency'),
                gateway: $request->input('gateway', 'paystack'),
                callbackUrl: route('mobile.wallet', [], false),
                metadata: ['source' => 'inline_checkout'],
            );

            return response()->json([
                'success' => true,
                ...$result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    })->name('payment.initialize');

    Route::post('/verify', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'reference' => ['required', 'string'],
        ]);

        $paymentService = app(\App\Application\Services\PaymentService::class);

        try {
            $result = $paymentService->verifyPayment($request->input('reference'));
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    })->name('payment.verify');
});

// Payment Gateway Checkout Flow Endpoints
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::post('/transaction/initialize', [\App\Http\Controllers\WalletDepositController::class, 'initialize'])->name('transaction.initialize');
    Route::match(['GET', 'POST'], '/wallet/verify/{reference}', [\App\Http\Controllers\WalletDepositController::class, 'verify'])->name('wallet.verify');
    Route::get('/wallet/balance', [\App\Http\Controllers\WalletDepositController::class, 'balance'])->name('wallet.balance');
    Route::post('/wallet/deposit/open-in-app', [\App\Http\Controllers\WalletDepositController::class, 'openInApp'])->name('wallet.deposit.openInApp');
    Route::get('/wallet/checkout/{reference}', [\App\Http\Controllers\Mobile\MobileAppController::class, 'checkout'])->name('mobile.wallet.checkout.web');
});

// Reference-based status & in-app callback routes (no auth session required)
Route::get('/wallet/deposit-status/{reference}', [\App\Http\Controllers\WalletDepositController::class, 'depositStatus'])->name('wallet.deposit-status');
Route::get('/wallet/callback', [\App\Http\Controllers\WalletDepositController::class, 'callback'])->name('wallet.deposit.callback');
Route::get('/wallet/deposit/callback', [\App\Http\Controllers\WalletDepositController::class, 'callback'])->name('wallet.callback');

// Webhook Endpoints
Route::post('/webhooks/flutterwave', [\App\Http\Controllers\WalletDepositController::class, 'handleWebhook'])->name('webhooks.flutterwave');
Route::post('/webhooks/paystack', [\App\Http\Controllers\WalletDepositController::class, 'handleWebhook'])->name('webhooks.paystack');

// Simora All-Services Virtual Number API v1 (Session Auth, JSON)
Route::prefix('api/v1')->group(function () {
    Route::get('/catalog/all-services', [\App\Http\Controllers\Api\V1\AllServicesOrderController::class, 'catalog'])
        ->name('web.api.v1.catalog');

    Route::middleware(['auth'])->group(function () {
        Route::get('/wallet', [\App\Http\Controllers\Api\V1\AllServicesOrderController::class, 'wallet'])
            ->name('web.api.v1.wallet');

        Route::post('/orders', [\App\Http\Controllers\Api\V1\AllServicesOrderController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('web.api.v1.orders.store');

        Route::get('/orders/{id}', [\App\Http\Controllers\Api\V1\AllServicesOrderController::class, 'show'])
            ->name('web.api.v1.orders.show');

        Route::get('/orders', [\App\Http\Controllers\Api\V1\AllServicesOrderController::class, 'index'])
            ->name('web.api.v1.orders.index');
    });
});




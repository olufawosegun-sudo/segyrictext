<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PricingController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\VerificationController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Global Virtual Number & SMS Verification Platform
|--------------------------------------------------------------------------
*/

// Authentication (Public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Carrier Inbound SMS Webhook Receivers (Public, provider verified)
Route::post('/v1/webhooks/providers/{provider}', [WebhookController::class, 'handleCarrierWebhook'])
    ->name('api.v1.webhooks.provider');

// Payment Gateway Inbound Webhook Receivers (Public, signature verified)
Route::post('/v1/webhooks/payments/{gateway}', [WebhookController::class, 'handlePaymentWebhook'])
    ->name('api.v1.webhooks.payment');

// PVADeals Webhook Receiver (Timing-safe secret verification, no CSRF)
Route::post('/webhooks/pvadeals/{secret}', [WebhookController::class, 'handlePvaDealsWebhook'])
    ->name('api.webhooks.pvadeals');
Route::post('/api/webhooks/pvadeals/{secret}', [WebhookController::class, 'handlePvaDealsWebhook']);

// API V1 Public Catalog Routes
Route::prefix('v1')->group(function () {
    Route::get('/countries', [CountryController::class, 'index'])->name('api.v1.countries.index');
    Route::get('/countries/{id}', [CountryController::class, 'show'])->name('api.v1.countries.show');

    Route::get('/services', [ServiceController::class, 'index'])->name('api.v1.services.index');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('api.v1.services.show');

    Route::get('/pricing', [PricingController::class, 'index'])->name('api.v1.pricing.index');

    // PVADeals Live Catalog & Locations
    Route::get('/pvadeals/catalog', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'index'])->name('api.v1.pvadeals.catalog');
    Route::get('/pvadeals/websites', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'websites'])->name('api.v1.pvadeals.websites');
    Route::get('/pvadeals/best-value', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'bestValue'])->name('api.v1.pvadeals.best-value');
    Route::get('/pvadeals/locations', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'locations'])->name('api.v1.pvadeals.locations');
    Route::get('/pvadeals/inventory', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'inventory'])->name('api.v1.pvadeals.inventory');
    Route::get('/pvadeals/countries', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'allServicesCountries'])->name('api.v1.pvadeals.countries');
    Route::get('/pvadeals/numbers/stats', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'numbersStats'])->name('api.v1.pvadeals.numbers.stats');
    Route::post('/pvadeals/sync', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'sync'])->name('api.v1.pvadeals.sync');
    Route::get('/locations', [\App\Http\Controllers\Api\V1\PvaServiceCatalogController::class, 'locations'])->name('api.v1.locations');
});

// API V1 Protected Endpoints (Sanctum Token or Web/Mobile Session)
Route::prefix('v1')->middleware(['web', 'auth:web,sanctum'])->group(function () {
    // Current User Profile
    Route::get('/user', [AuthController::class, 'user'])->name('api.v1.user');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');

    // Wallet Ledger & Balance
    Route::get('/wallet/balance', [WalletController::class, 'balance'])->name('api.v1.wallet.balance');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('api.v1.wallet.transactions');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('api.v1.wallet.deposit');

    // Online Payments & Gateways (Paystack, Flutterwave, Mock)
    Route::get('/payments/methods', [PaymentController::class, 'methods'])->name('api.v1.payments.methods');
    Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('api.v1.payments.initiate');
    Route::get('/payments/verify/{reference}', [PaymentController::class, 'verify'])->name('api.v1.payments.verify');
    Route::post('/payments/charge/bank-transfer', [PaymentController::class, 'chargeBankTransfer'])->name('api.v1.payments.charge.bank-transfer');
    Route::post('/payments/charge/ussd', [PaymentController::class, 'chargeUssd'])->name('api.v1.payments.charge.ussd');
    Route::post('/payments/charge/card', [PaymentController::class, 'chargeCard'])->name('api.v1.payments.charge.card');
    Route::post('/payments/charge/card/submit-otp', [PaymentController::class, 'submitCardOtp'])->name('api.v1.payments.charge.card.submit-otp');
    Route::post('/payments/charge/simulate-success', [PaymentController::class, 'simulateSuccess'])->name('api.v1.payments.charge.simulate-success');
    Route::post('/payments/{reference}/expire', [PaymentController::class, 'expirePayment'])->name('api.v1.payments.expire');

    // Virtual Number Allocation & Verification Lifecycle
    Route::post('/verifications/request', [VerificationController::class, 'requestVerification'])->name('api.v1.verifications.request');
    Route::get('/verifications/{id}', [VerificationController::class, 'show'])->name('api.v1.verifications.show');
    Route::post('/verifications/{id}/cancel', [VerificationController::class, 'cancel'])->name('api.v1.verifications.cancel');

    // Carrier Provider Management (PVADeals Key & Catalog Sync)
    Route::get('/provider/pvadeals/status', [\App\Http\Controllers\Api\V1\ProviderSettingsController::class, 'getStatus'])->name('api.v1.provider.pvadeals.status');
    Route::post('/provider/pvadeals/key', [\App\Http\Controllers\Api\V1\ProviderSettingsController::class, 'saveKey'])->name('api.v1.provider.pvadeals.key');
    Route::post('/provider/pvadeals/sync', [\App\Http\Controllers\Api\V1\ProviderSettingsController::class, 'syncCatalog'])->name('api.v1.provider.pvadeals.sync');
});

<?php

use App\Http\Controllers\Mobile\MobileAppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dedicated Mobile Routes (NativePHP Mobile Runtime)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/home', [MobileAppController::class, 'home'])->name('mobile.home');
    Route::get('/mobile', [MobileAppController::class, 'home'])->name('mobile.home.prefix');
    Route::get('/get-number', [MobileAppController::class, 'getNumber'])->name('mobile.get-number');
    Route::get('/sessions', [MobileAppController::class, 'sessions'])->name('mobile.sessions');
    Route::get('/wallet', [MobileAppController::class, 'wallet'])->name('mobile.wallet');
    Route::get('/mobile/wallet', [MobileAppController::class, 'wallet']);
    Route::get('/deposit', [MobileAppController::class, 'deposit'])->name('mobile.deposit');
    Route::get('/mobile/deposit', [MobileAppController::class, 'deposit']);
    Route::get('/wallet/checkout/{reference}', [MobileAppController::class, 'checkout'])->name('mobile.wallet.checkout');
    Route::get('/library', [MobileAppController::class, 'library'])->name('mobile.library.index');
    Route::get('/editor', [MobileAppController::class, 'editor'])->name('mobile.editor.index');
    Route::post('/documents', [MobileAppController::class, 'store'])->name('mobile.documents.store');
    Route::get('/documents/{id}', [MobileAppController::class, 'show'])->name('mobile.documents.show');
    Route::get('/documents/{id}/edit', [MobileAppController::class, 'edit'])->name('mobile.documents.edit');
    Route::put('/documents/{id}', [MobileAppController::class, 'update'])->name('mobile.documents.update');
    Route::post('/documents/{id}/process', [MobileAppController::class, 'process'])->name('mobile.documents.process');
    Route::get('/activity', [MobileAppController::class, 'activity'])->name('mobile.activity.index');
    Route::get('/settings', [MobileAppController::class, 'settings'])->name('mobile.settings.index');
});

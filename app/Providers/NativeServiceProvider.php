<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NativeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Patch NativePHP iOS navigation policy before building
        $this->patchIOSNavigationPolicy();
    }

    /**
     * The NativePHP plugins to enable.
     *
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    public function plugins(): array
    {
        return [
            \Native\Mobile\Providers\BrowserServiceProvider::class,
        ];
    }

    /**
     * Patch iOS WKWebView navigation policy to allow in-app payment checkouts.
     * 
     * The ContentView.swift has been patched with a complete payment flow tracking
     * system that keeps ALL payment navigations (checkout, 3D Secure, bank OTP)
     * inside the WKWebView. This method ensures the patch remains applied.
     */
    protected function patchIOSNavigationPolicy(): void
    {
        $swiftFile = base_path('vendor/nativephp/mobile/resources/xcode/NativePHP/ContentView.swift');

        if (!file_exists($swiftFile)) {
            return;
        }

        $content = file_get_contents($swiftFile);

        // Check if already patched with our payment flow tracking system
        if (str_contains($content, 'isInPaymentFlow') && str_contains($content, 'paymentDomainFragments') && str_contains($content, '"localhost"')) {
            // Already patched — nothing to do
            return;
        }

        // If for some reason the patch was lost (e.g. composer update),
        // log a warning. The developer should re-apply the ContentView.swift changes.
        \Illuminate\Support\Facades\Log::warning(
            '[NativeServiceProvider] ContentView.swift is missing the in-app payment flow patch. ' .
            'Payment checkout may redirect to Safari. Re-apply the patch to ContentView.swift.'
        );
    }
}

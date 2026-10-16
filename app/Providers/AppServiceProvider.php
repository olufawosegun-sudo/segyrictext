<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Domain\Verification\Services\OtpExtractorInterface::class,
            \App\Domain\Verification\Services\OtpExtractor::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Provide an inline base64 brand logo data URI to guarantee instant, flawless rendering
        // on all mobile WebViews (especially iOS WKWebView which rejects proxy duplicate headers)
        $logoDataUri = cache()->rememberForever('verixa_brand_logo_base64', function () {
            $b64Path = public_path('images/verixa-b64.txt');
            if (file_exists($b64Path)) {
                return trim(file_get_contents($b64Path));
            }
            $imgPath = public_path('images/verixa-thumb.png');
            if (file_exists($imgPath)) {
                return 'data:image/png;base64,' . base64_encode(file_get_contents($imgPath));
            }
            return asset('images/verixa.png');
        });

        \Illuminate\Support\Facades\View::share('brandLogo', $logoDataUri);

        \Illuminate\Support\Facades\View::composer('web.layouts.app', function ($view) {
            $announcement = \Illuminate\Support\Facades\Schema::hasTable('announcements')
                ? \App\Models\Announcement::active()->first()
                : null;
            $view->with('activeAnnouncement', $announcement);
        });
    }
}

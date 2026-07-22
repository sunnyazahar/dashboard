<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Keep generated links aligned with APP_URL (includes /public when
        // the host document root is the project folder, not public/).
        URL::forceRootUrl(rtrim((string) config('app.url'), '/'));

        $assetUrl = config('app.asset_url') ?: env('ASSET_URL');
        if (is_string($assetUrl) && $assetUrl !== '') {
            URL::useAssetOrigin(rtrim($assetUrl, '/'));
        }
    }
}

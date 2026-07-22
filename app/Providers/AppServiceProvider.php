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
        $appUrl = rtrim((string) config('app.url'), '/');
        URL::forceRootUrl($appUrl);

        // Static theme assets live in public/files. On hosts where the
        // document root is the project folder, those URLs need /public.
        $assetUrl = config('app.asset_url') ?: env('ASSET_URL') ?: $appUrl;
        $assetUrl = rtrim((string) $assetUrl, '/');

        if ($assetUrl !== '' && ! str_ends_with(strtolower($assetUrl), '/public')) {
            $assetUrl .= '/public';
        }

        config(['app.asset_url' => $assetUrl]);
        URL::useAssetOrigin($assetUrl);
    }
}

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
        $root = $this->normalizePublicUrl((string) config('app.url'));
        config(['app.url' => $root]);

        $assetUrl = config('app.asset_url') ?: env('ASSET_URL');
        if (is_string($assetUrl) && $assetUrl !== '') {
            $assetUrl = $this->normalizePublicUrl($assetUrl);
            config(['app.asset_url' => $assetUrl]);
            URL::useAssetOrigin($assetUrl);
        } else {
            config(['app.asset_url' => null]);
            URL::useAssetOrigin(null);
        }

        // Keep generated links/assets on the app root (never .../public).
        URL::forceRootUrl($root);
    }

    /**
     * Document root should be the public/ folder, so APP_URL must not end with /public.
     */
    private function normalizePublicUrl(string $url): string
    {
        $url = rtrim($url, '/');

        return (string) preg_replace('#/public$#i', '', $url);
    }
}

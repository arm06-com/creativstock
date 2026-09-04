<?php

namespace App\Providers;

use App\Http\Middleware\EnsureUserHasPermission;
use App\Models\SiteSetting;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->registerRouteMiddleware();
        $this->configureSiteSettings();
    }

    protected function registerRouteMiddleware(): void
    {
        app('router')->aliasMiddleware('permission', EnsureUserHasPermission::class);
    }

    protected function configureSiteSettings(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $siteSettings = SiteSetting::current();

        config([
            'app.name' => $siteSettings->site_title ?: config('app.name', 'Creativ Stock'),
            'app.meta_description' => $siteSettings->meta_description ?: config('app.meta_description', 'Marketplace for creative assets.'),
            'paddle.environment' => $siteSettings->paddle_environment ?: config('paddle.environment', 'sandbox'),
            'paddle.api_key' => $this->decryptSettingValue($siteSettings->paddle_api_key, config('paddle.api_key')),
            'paddle.client_token' => $this->decryptSettingValue($siteSettings->paddle_client_token, config('paddle.client_token')),
            'paddle.webhook_secret' => $this->decryptSettingValue($siteSettings->paddle_webhook_secret, config('paddle.webhook_secret')),
            'paddle.test_price_id' => $siteSettings->paddle_test_price_id ?: config('paddle.test_price_id'),
            'paddle.verify_ssl' => $siteSettings->paddle_verify_ssl !== null ? (bool) $siteSettings->paddle_verify_ssl : config('paddle.verify_ssl', true),
        ]);
    }

    protected function decryptSettingValue(?string $storedValue, ?string $fallback = null): ?string
    {
        if (blank($storedValue)) {
            return $fallback;
        }

        try {
            return Crypt::decryptString($storedValue);
        } catch (DecryptException) {
            return $storedValue;
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}

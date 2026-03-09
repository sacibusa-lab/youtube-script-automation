<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\AI\AIManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate Limiter for AI Image Generation
        \Illuminate\Support\Facades\RateLimiter::for('images', function (object $job) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($job->userId());
        });

        // Globally share site settings with all views
        try {
            $siteSettings = Cache::rememberForever('site_settings', function () {
                // Ensure the table exists before attempting to query (prevents migration errors)
                if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                    return AppSetting::pluck('value', 'key')->toArray();
                }
                return [];
            });
            View::share('siteSettings', $siteSettings);

            // Apply custom mail configuration if available
            if (!empty($siteSettings['mail_host'])) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $siteSettings['mail_host'],
                    'mail.mailers.smtp.port' => $siteSettings['mail_port'] ?? 587,
                    'mail.mailers.smtp.username' => $siteSettings['mail_username'] ?? null,
                    'mail.mailers.smtp.password' => $siteSettings['mail_password'] ?? null,
                    'mail.mailers.smtp.encryption' => ($siteSettings['mail_encryption'] === 'null' ? null : ($siteSettings['mail_encryption'] ?? 'tls')),
                    'mail.from.address' => $siteSettings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $siteSettings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Exception $e) {
            // Failsafe if DB isn't ready
            View::share('siteSettings', []);
        }
    }
}

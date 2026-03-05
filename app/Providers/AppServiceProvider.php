<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    }
}

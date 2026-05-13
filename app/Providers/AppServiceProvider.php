<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Illuminate\Support\Facades\RateLimiter;
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
    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
        RateLimiter::for('narrow', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('normal', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });
    }
}

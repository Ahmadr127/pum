<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Firebase service as singleton
        $this->app->singleton(\App\Services\FirebaseService::class, function ($app) {
            return new \App\Services\FirebaseService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom middleware
        $this->app['router']->aliasMiddleware('permission', \App\Http\Middleware\CheckPermission::class);

        // Force HTTPS secara absolut di server production
        // Mengatasi server Nginx yang tidak meneruskan X-Forwarded-Proto 
        // dan menghindari masalah cache .env.
        if (
            (isset($_SERVER['HTTP_HOST']) && !str_contains($_SERVER['HTTP_HOST'], 'localhost') && !str_contains($_SERVER['HTTP_HOST'], '127.0.0.1')) ||
            str_starts_with(config('app.url', ''), 'https://')
        ) {
            URL::forceScheme('https');
        }
    }
}

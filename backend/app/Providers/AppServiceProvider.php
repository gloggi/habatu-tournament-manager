<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

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
        Socialite::extend('midata', function ($app) {
            $config = $app['config']['services.midata'];

            return new MiDataProvider(
                $app['request'], $config['client_id'],
                $config['client_secret'], $config['redirect']
            );
        });
    }
}

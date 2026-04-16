<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyCartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    
        $this->app->singleton(CartRepositoryInterface::class, function ($app) {
            return new CartRepository();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace Maxfactor\Checkout;

use Illuminate\Support\ServiceProvider;

class CheckoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/config/maxfactor-checkout.php' => config_path('maxfactor-checkout.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/maxfactor-checkout.php', 'maxfactor-checkout');

        $this->app->bind('Maxfactor\Checkout\Contracts\Checkout', config('maxfactor-checkout.driver'));
        // dd($this->app->make('router'));
        $this->app->when(config('maxfactor-checkout.driver'))
            ->needs('$content')
            ->give(request()->route('uid'));
    }
}

<?php

namespace Maxfactor\Checkout;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CheckoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        AliasLoader::getInstance([
            'Payment' => '\Maxfactor\Checkout\Handlers\Payment',
            'Paypal' => '\Maxfactor\Checkout\Handlers\Paypal',
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Maxfactor\Checkout\CheckoutController');
    }
}

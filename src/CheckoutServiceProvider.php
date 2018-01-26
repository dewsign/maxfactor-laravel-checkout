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
        $this->bootRoutes();
        $this->bootConfig();
        $this->bootTranslations();
        $this->bootViews();
        $this->bootAssets();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerBindings();
    }

    private function bootRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    private function bootConfig()
    {
        $this->publishes([
            __DIR__.'/config/maxfactor-checkout.php' => config_path('maxfactor-checkout.php')
        ]);
    }

    private function bootTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'maxfactor');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/maxfactor'),
        ]);
    }

    private function bootViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'maxfactor');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/maxfactor'),
        ]);
    }

    private function bootAssets()
    {
        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js/vendor/maxfactor'),
        ], 'js');
    }

    private function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/config/maxfactor-checkout.php', 'maxfactor-checkout');
    }

    private function registerBindings()
    {
        $this->app->bind('Maxfactor\Checkout\Contracts\Checkout', config('maxfactor-checkout.driver'));
    }
}

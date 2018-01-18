<?php

$siteUrl = parse_url(config('app.url'));

Route::domain($siteUrl['host'])->group(function () {
    Route::group(['middleware' => ['web', 'doNotCacheResponse'], 'prefix' => 'checkout', 'as' => 'checkout.'], function () {
        Route::get('/', 'Maxfactor\Checkout\CheckoutController@index')->name('index');
        Route::get('{uid}/{stage?}', 'Maxfactor\Checkout\CheckoutController@show')->name('show');
        Route::post('{uid}/{stage?}', 'Maxfactor\Checkout\CheckoutController@store')->name('store');
    });
});

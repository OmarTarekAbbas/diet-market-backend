<?php

/*
|--------------------------------------------------------------------------
| Cart Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Modules\Cart\Controllers\Site',
    //    'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
], function () {
    // for authorization user
    Route::group([
        'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
    ], function () {
        Route::get('/cart', 'CartController@index');
        Route::post('/cart', 'CartController@store');
        Route::put('/cart/{id}', 'CartController@update');
        Route::delete('/cart/{id}', 'CartController@destroy');
        Route::delete('/cart', 'CartController@flush');
    });

    // for visitor user
    Route::get('/cart', 'CartController@index');
    Route::post('/cart', 'CartController@store');
    Route::put('/cart/{id}', 'CartController@update');
    Route::delete('/cart/{id}', 'CartController@destroy');
    Route::delete('/cart', 'CartController@flush');

    Route::get('/shippingTime', 'ShippingTimeController@shippingTime');
    Route::get('/shippingTimeWeeks', 'ShippingTimeController@shippingTimeWeeks');

    Route::get('removeCouponCode', 'CartController@removeCouponCode');
});

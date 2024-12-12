<?php
/*
|--------------------------------------------------------------------------
| Wallet Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Wallet\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/wallet', 'WalletController@index');

    Route::get('/walletDelivery', 'WalletDeliveryController@index');
    // Child routes
});

Route::group([
    'prefix' => '/deliveryMen',
], function () {
    Route::group([
        'namespace' => 'Modules\Wallet\Controllers\Site',
    ], function () {
        Route::group([
            'middleware' => ['logged-in'],
        ], function () {
            Route::get('/walletDelivery', 'WalletDeliveryController@index');
        });
    });
});

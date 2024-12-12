<?php

/*
|--------------------------------------------------------------------------
| Wallet Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Wallet\Controllers\Admin',
    'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
], function () {
    // Sub API CRUD routes
    Route::post('/wallet/withdraw', 'WalletController@withdraw');
    Route::post('/wallet/deposit', 'WalletController@deposit');
    // Main API CRUD routes
    Route::apiResource('/wallet', 'WalletController');

    Route::apiResource('/walletDelivery', 'WalletDeliveryController');
    Route::post('/walletDelivery/withdraw', 'WalletDeliveryController@withdraw');
    Route::post('/walletDelivery/deposit', 'WalletDeliveryController@deposit');

    Route::apiResource('/walletProvider', 'WalletProviderController');
    Route::post('/walletProvider/withdraw', 'WalletProviderController@withdraw');
    Route::post('/walletProvider/deposit', 'WalletProviderController@deposit');
});

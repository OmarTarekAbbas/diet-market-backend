<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Transactions Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Transactions\Controllers\Site',
], function () {
    // list records
    Route::get('/transactions', 'TransactionsController@index');

    Route::get('/seller-wallet', 'TransactionsController@sellerWallet');
    // one record

    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/transactions', 'TransactionsController@index');
        Route::get('/transactions/{id}', 'TransactionsController@show');
    });

    Route::group([
        'prefix' => '/restaurantManager',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/transactions', 'TransactionsController@index');
        Route::get('/transactions/{id}', 'TransactionsController@show');
    });

    Route::group([
        'prefix' => '/nutritionSpecialistMangers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/transactions', 'TransactionsController@index');
        Route::get('/transactions/{id}', 'TransactionsController@show');
    });

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/transactions', 'TransactionsController@index');
        Route::get('/transactions/{id}', 'TransactionsController@show');
    });
});

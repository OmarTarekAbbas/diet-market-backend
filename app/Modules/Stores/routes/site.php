<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stores Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Stores\Controllers\Site',
], function () {
    // list records
    Route::get('/stores', 'StoresController@index');
    // one record
    Route::get('/stores/{id}', 'StoresController@show');
    // Child routes
    Route::group([
        'middleware' => ['logged-in', 'isStoreManager'],
        //'prefix' => 'storeManagers'
    ], function () {
        Route::post('/storeManagers/my-store', 'StoresController@updateMyStore');
        Route::get('/storeManagers/my-store', 'StoresController@getMyStore');
    });
});

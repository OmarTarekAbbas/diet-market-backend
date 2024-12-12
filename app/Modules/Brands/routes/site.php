<?php

/*
|--------------------------------------------------------------------------
| Brands Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Brands\Controllers\Site',
], function () {
    // list records
    Route::get('/brands', 'BrandsController@index');
    // one record
    Route::get('/brands/{id}', 'BrandsController@show');
    // Child routes

    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/brands', 'BrandsController@index');
        Route::get('/brands/{id}', 'BrandsController@show');
    });
});

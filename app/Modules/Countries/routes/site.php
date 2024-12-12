<?php

/*
|--------------------------------------------------------------------------
| Countries Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Countries\Controllers\Site',
], function () {
    // list records
    Route::get('/countries', 'CountriesController@index');
    // one record
    Route::get('/countries/{id}', 'CountriesController@show');
    // Child routes

    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/countries', 'CountriesController@index');
        Route::get('/countries/{id}', 'CountriesController@show');
    });

    Route::group([
        'prefix' => '/restaurantManager',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/countries', 'CountriesController@index');
        Route::get('/countries/{id}', 'CountriesController@show');
    });
    Route::group([
        'prefix' => '/nutritionSpecialistMangers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/countries', 'CountriesController@index');
        Route::get('/countries/{id}', 'CountriesController@show');
    });

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/cities', 'CitiesController@index');
        Route::get('/cities/{id}', 'CitiesController@show');
    });
});

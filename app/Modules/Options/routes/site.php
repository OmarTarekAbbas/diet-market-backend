<?php

/*
|--------------------------------------------------------------------------
| Options Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Options\Controllers\Site',
], function () {
    // Main API CRUD routes
    Route::get('/options', 'OptionsController@index');
    Route::get('/options/{id}', 'OptionsController@show');

    Route::group(['prefix' => '/storeManagers'], function () {
        Route::post('/options', 'OptionsController@store');
        Route::put('/options/{id}', 'OptionsController@update');
        Route::delete('/options/{id}', 'OptionsController@destroy');
    });

    Route::get('/filters', 'OptionsController@filters');


    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/options', 'OptionsController@index');
        Route::get('/options/{id}', 'OptionsController@show');
    });

    Route::group([
        'prefix' => '/restaurantManager',
        'middleware' => ['logged-in'],
    ], function () {
        Route::get('/options', 'OptionsController@index');
        Route::get('/options/{id}', 'OptionsController@show');
    });
});

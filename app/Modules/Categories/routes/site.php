<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Categories Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Categories\Controllers\Site',
], function () {
    // list records
    Route::get('/categories', 'CategoriesController@index');
    // one record
    Route::get('/categories/{id}', 'CategoriesController@show');

    //show Items For Category
    Route::get('/categories-restaurant/{id}', 'CategoriesController@showCategoriesRestaurant');

    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/categories', 'CategoriesController@index');
        Route::get('/categories/{id}', 'CategoriesController@show');
    });

    Route::group([
        'prefix' => '/restaurantManager',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/categories', 'CategoriesController@index');
        Route::get('/categories/{id}', 'CategoriesController@show');
    });

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/categories', 'CategoriesController@index');
        Route::get('/categories/{id}', 'CategoriesController@show');
    });
});

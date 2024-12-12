<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Products Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Products\Controllers\Site',
], function () {
    Route::group(['middleware' => ['logged-in']], function () {
        Route::post('/products/reviews', 'ProductReviewsController@store');
    });

    Route::get('/products/reviews', 'ProductReviewsController@index');

    // Main API CRUD routes
    // Route::apiResource('/products', 'ProductsController');
    Route::get('/products', 'ProductsController@index');
    Route::get('/products/{id}', 'ProductsController@show');

    // Main API CRUD routes products
    Route::get('/productMeals', 'ProductMealsController@index');
    Route::get('/productMeals/{id}', 'ProductMealsController@show');

    // Route::get('/listProductPackageSizes', 'ProductPackageSizeController@index');
    // Route::get('/showProductPackageSizes/{id}', 'ProductPackageSizeController@show');

    Route::group([
        'prefix' => '/storeManagers',
        'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::post('/products', 'ProductsController@store');
        Route::get('/products', 'ProductsController@getMyStore');
        Route::get('/products/{id}', 'ProductsController@show');
        Route::put('/products/{id}', 'ProductsController@update');
        Route::delete('/products/{id}', 'ProductsController@destroy');
        Route::get('/Listproducts/reviews', 'ProductReviewsController@index');
        
    });

    Route::group([
        'prefix' => '/restaurantManager',
        // 'middleware' => ['restaurantManager'],
        'middleware' => ['logged-in'],

    ], function () {
        Route::get('/productMeals', 'ProductMealsController@getMyStore');
        Route::post('/productMeals', 'ProductMealsController@store');
        Route::get('/productMeals/{id}', 'ProductMealsController@show');
        Route::put('/productMeals/{id}', 'ProductMealsController@update');
        Route::delete('/productMeals/{id}', 'ProductMealsController@destroy');
    });
});


Route::group([
    'namespace' => 'Modules\Products\Controllers\Site',
    'prefix' => '/storeManagers',
    'middleware' => ['logged-in', 'isStoreManager'],
], function () {
    Route::get('/listProductPackageSizes', 'ProductPackageSizeController@index');
    Route::get('/showProductPackageSizes', 'ProductPackageSizeController@show');
    
});

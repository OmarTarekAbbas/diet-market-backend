<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Restaurants Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Restaurants\Controllers\Site',
], function () {
    // list records
    Route::get('/restaurants', 'RestaurantsController@index');
    // one record
    Route::get('/restaurants/{id}', 'RestaurantsController@show');
    // Child routes
    Route::post('/checkDistance', 'RestaurantsController@checkDistance');

    Route::group(['middleware' => ['logged-in']], function () {
        Route::post('/restaurants/reviews', 'RestaurantsReviewsController@store');
    });

    Route::get('/listRestaurants/reviews', 'RestaurantsReviewsController@index');
    Route::get('/reasonRestaurant', 'ReasonRestaurantController@index');
    Route::get('/deliveryRestaurant', 'DeliveryRestaurantController@index');

    Route::group([
        'prefix' => '/restaurantManager',
        // 'middleware' => ['restaurantManager'],
    ], function () {
        Route::get('/my-restaurants', 'RestaurantsController@getMyRestaurants');
        Route::post('/my-restaurants', 'RestaurantsController@updateMyRestaurants');
        Route::get('/listRestaurants/reviews', 'RestaurantsReviewsController@index');
    });
});

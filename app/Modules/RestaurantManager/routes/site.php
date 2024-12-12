<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RestaurantManager Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

// Route::group([
//     'namespace' => 'Modules\RestaurantManager\Controllers\Site',
// ], function () {
//     // list records
//     Route::get('/restaurantManager', 'RestaurantManagerController@index');
//     // one record
//     Route::get('/restaurantManager/{id}', 'RestaurantManagerController@show');
//     // Child routes
// });



Route::group([
    'namespace' => 'Modules\RestaurantManager\Controllers\Site',
], function () {
    Route::post('restaurantManager/login', 'Auth\LoginController@index');
    Route::post('restaurantManager/forget-password', 'Auth\ForgetPasswordController@index');
    Route::post('restaurantManager/reset-password', 'Auth\ResetPasswordController@index');

    Route::group([
        // 'middleware' => ['restaurantManager'],
        'prefix' => 'restaurantManager',
    ], function () {
        Route::get('me', 'UpdateAccountController@me');
        Route::post('me', 'UpdateAccountController@index');
        Route::post('update-password', 'UpdateAccountController@updatePassword');
        Route::post('logout', 'Auth\LogoutController@index');
        Route::post('add-device-token', 'UpdateAccountController@addDeviceToken');
        Route::post('remove-device-token', 'UpdateAccountController@removeDeviceToken');
    });
});

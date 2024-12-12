<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NutritionSpecialistMangers Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\NutritionSpecialistMangers\Controllers\Site',
], function () {
    // list records
    Route::get('/nutritionSpecialist', 'NutritionSpecialistMangersController@index');
    // one record
    Route::get('/nutritionSpecialist/{id}', 'NutritionSpecialistMangersController@show');
    // Child routes


    Route::post('nutritionSpecialistMangers/login', 'Auth\LoginController@index');
    Route::post('nutritionSpecialistMangers/forget-password', 'Auth\ForgetPasswordController@index');
    Route::post('nutritionSpecialistMangers/reset-password', 'Auth\ResetPasswordController@index');
    Route::group([
        // 'middleware' => ['nutritionSpecialistMangers'],
        'prefix' => 'nutritionSpecialistMangers',
    ], function () {
        Route::get('me', 'UpdateAccountController@me');
        Route::post('me', 'UpdateAccountController@index');
        Route::post('update-password', 'UpdateAccountController@updatePassword');
        Route::post('logout', 'Auth\LogoutController@index');
        Route::post('add-device-token', 'UpdateAccountController@addDeviceToken');
        Route::post('remove-device-token', 'UpdateAccountController@removeDeviceToken');
    });
});

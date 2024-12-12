<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ClubManagers Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\ClubManagers\Controllers\Site',
], function () {
    Route::post('/clubManagers/login', 'Auth\LoginController@index');
    Route::post('/clubManagers/forget-password', 'Auth\ForgetPasswordController@index');
    Route::post('/clubManagers/reset-password', 'Auth\ResetPasswordController@index');
    // Child routes

    Route::group([
        'middleware' => ['logged-in'],
        'prefix' => 'clubManagers',
    ], function () {
        Route::get('me', 'UpdateAccountController@me');
        Route::post('me', 'UpdateAccountController@index');
        Route::post('update-password', 'UpdateAccountController@updatePassword');
        Route::post('logout', 'Auth\LogoutController@index');
        Route::post('add-device-token', 'UpdateAccountController@addDeviceToken');
        Route::post('remove-device-token', 'UpdateAccountController@removeDeviceToken');
    });
});

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| StoreManagers Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\StoreManagers\Controllers\Site',
    'prefix' => '/storeManagers',
], function () {
    Route::post('/login', 'Auth\LoginController@index');
    Route::post('/forget-password', 'Auth\ForgetPasswordController@index');
    Route::post('/reset-password', 'Auth\ResetPasswordController@index');

    Route::group([
        'middleware' => ['logged-in'],
    ], function () {
        Route::get('/me', 'UpdateAccountController@me');
        Route::post('/me', 'UpdateAccountController@index');
        Route::post('/update-password', 'UpdateAccountController@updatePassword');
        Route::post('/logout', 'Auth\LogoutController@index');
        Route::post('/add-device-token', 'UpdateAccountController@addDeviceToken');
        Route::post('/remove-device-token', 'UpdateAccountController@removeDeviceToken');
    });
});
Route::group([
    'namespace' => 'Modules\StoreManagers\Controllers\Site',
], function () {
    Route::get('/store', 'StoreManagersController@index');
});

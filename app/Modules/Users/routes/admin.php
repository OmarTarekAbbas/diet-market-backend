<?php

/*
|--------------------------------------------------------------------------
| Users Admin Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/
Route::post('/login', 'Modules\Users\Controllers\Admin\Auth\LoginController@index')->name('login');
Route::post('/forget-password', 'Modules\Users\Controllers\Admin\Auth\ForgetPasswordController@index');
Route::post('/reset-password', 'Modules\Users\Controllers\Admin\Auth\ResetPasswordController@index');
Route::get('/logout', 'Modules\Users\Controllers\Admin\Auth\LogoutController@index')->name('logout');

Route::group([
    'middleware' => ['logged-in'],
    'namespace' => 'Modules\Users\Controllers\Site',

], function () {
    Route::post('users/me', 'UpdateAccountController@index');
    Route::get('users/me', 'UpdateAccountController@me');
    Route::post('/logout', 'Auth\LogoutController@index');

    Route::post('/add-device-token', 'Auth\DeviceTokensController@addDeviceToken');
    Route::post('/remove-device-token', 'Auth\DeviceTokensController@removeDeviceToken');

    Route::post('update-phone', 'UpdateAccountController@updatePhoneNumber');
    Route::post('update-phone/verify', 'UpdateAccountController@verifyUpdatedPhoneNumber');

    Route::post('users/update-password', 'UpdateAccountController@updatePassword');
});

Route::group([
    'namespace' => 'Modules\Users\Controllers\Admin',
    'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
], function () {
    // Restful API CRUD routes
    Route::apiResource('/users/permissions', 'PermissionsController');
    Route::apiResource('/users/groups', 'UsersGroupsController');
    Route::apiResource('/users', 'UsersController');
    // Child API CRUD routes
});

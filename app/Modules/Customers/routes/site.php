<?php

/*
|--------------------------------------------------------------------------
| Customers Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Customers\Controllers\Site',
], function () {
    // list records
    Route::post('/send-code', 'Auth\LoginController@sendLoginOTP');
    Route::post('/login', 'Auth\LoginController@index');
    Route::post('/register', 'Auth\RegisterController@index');
    Route::post('/forget-password', 'Auth\ForgetPasswordController@index');
    Route::post('/reset-password', 'Auth\ResetPasswordController@index');
    Route::post('/verify-reset-code', 'Auth\ResetPasswordController@verify');
    Route::post('/create-account', 'Auth\RegisterController@index');
    Route::post('/resend-code', 'Auth\LoginController@resendCode');
    Route::post('/register/verify', 'Auth\RegisterController@verify');
    Route::post('/register/resend-code', 'Auth\RegisterController@resendVerificationCode');

    Route::post('/all/verify', 'Auth\RegisterController@allVerify');
    Route::post('resend/all/verify', 'Auth\RegisterController@resendAllVerify');
    Route::post('delete/Profile', 'UpdateAccountController@deleteProfile');


    Route::group([
        'middleware' => ['logged-in'],
    ], function () {
        Route::post('/me', 'UpdateAccountController@index');
        Route::post('/meWeb', 'UpdateAccountController@indexWeb');
        Route::get('/me', 'UpdateAccountController@me');
        Route::post('/logout', 'Auth\LogoutController@index');

        Route::post('/add-device-token', 'Auth\DeviceTokensController@addDeviceToken');
        Route::post('/remove-device-token', 'Auth\DeviceTokensController@removeDeviceToken');

        Route::post('update-phone', 'UpdateAccountController@updatePhoneNumber');
        Route::post('update-phone/verify', 'UpdateAccountController@verifyUpdatedPhoneNumber');

        Route::post('/update-password', 'UpdateAccountController@updatePassword');
    });
    // Child routes

    Route::group([
        'prefix' => '/nutritionSpecialistMangers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/customers', 'CustomersController@index');
        Route::get('/customers/{id}', 'CustomersController@show');
    });

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/customers', 'CustomersController@index');
        Route::get('/customers/{id}', 'CustomersController@show');
    });
});

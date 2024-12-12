<?php
/*
|--------------------------------------------------------------------------
| DeliveryMen Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/deliveryMen',
], function () {
    Route::group([
        'namespace' => 'Modules\DeliveryMen\Controllers\Site',
    ], function () {
        Route::post('/login', 'Auth\LoginController@index');
        Route::post('/forget-password', 'Auth\ForgetPasswordController@index');
        Route::post('/reset-password', 'Auth\ResetPasswordController@index');
        Route::post('/register', 'Auth\RegisterController@index');
        Route::post('/register/verify', 'Auth\RegisterController@verify');
        // Route::get('/WebView', 'DeliveryMenController@deliveryMenWebView');
        Route::get('/allSettingDeliveryMen', 'DeliveryMenController@allSettingDeliveryMen');
        Route::post('/verify', 'Auth\RegisterController@allVerify');
        Route::post('/resend/verify', 'Auth\RegisterController@resendAllVerify');
        Route::post('delete/Profile', 'UpdateAccountController@deleteProfile');


        Route::group([
            'middleware' => ['logged-in'],
        ], function () {
            // orders
            // Route::get('/orders', 'OrdersController@index');
            // Route::patch('/orders/{orderId}/startedMoving', 'OrdersController@startedMoving');
            // Route::get('/orders/{id}', 'OrdersController@show');
            // Route::post('/orders/{orderId}/{status}', 'OrdersController@changeStatus');

            Route::get('/orders/new-request', 'OrdersController@requestOrder');
            Route::get('/orders/current', 'OrdersController@ordersCurrent');
            Route::get('/orders/history', 'OrdersController@listCompleted');
            Route::get('/orders/now', 'OrdersController@/* A function that is used to get the current
            and new request orders. */
            listCurrentAndnewRequest');
            Route::post('/orders/deliveryOnTheWay', 'OrdersController@deliveryOnTheWay');
            Route::post('/orders/{id}/completed', 'OrdersController@deliveryCompletedOrder');
            Route::post('/orders/{id}/notCompleted', 'OrdersController@deliveryNotCompletedOrder');
            Route::get('/orders/{id}', 'OrdersController@show');
            Route::get('/orders/{id}/accepted', 'OrdersController@requestOrderAccepted');
            Route::get('/orders/{id}/rejected', 'OrdersController@requestOrderRejected');
            Route::get('/deliveryReasons-rejected', 'OrdersController@deliveryReasonsRejected');
            Route::get('/deliveryReasons-notCompleted', 'OrdersController@deliveryReasonsNotCompleted');
            Route::get('me/location-listRestaurant', 'OrdersController@locationListRestaurant');
            Route::get('/me', 'UpdateAccountController@me');
            Route::post('/me', 'UpdateAccountController@index');
            Route::post('/me/updateVehicle', 'UpdateAccountController@updateVehicle');
            Route::post('/me/updateImage', 'UpdateAccountController@updateImage');
            Route::post('/me/updateBank', 'UpdateAccountController@updateBank');
            Route::post('/me/updateLocation', 'UpdateAccountController@updateLocation');
            Route::post('/me/status', 'UpdateAccountController@updateStatus');
            Route::post('/update-password', 'UpdateAccountController@updatePassword');
            Route::post('/logout', 'Auth\LogoutController@index');
            Route::post('/add-device-token', 'Auth\DeviceTokensController@addDeviceToken');
            Route::post('/remove-device-token', 'Auth\DeviceTokensController@removeDeviceToken');
        });
    });
});

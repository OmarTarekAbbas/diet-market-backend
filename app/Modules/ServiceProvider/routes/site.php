<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ServiceProvider Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group(
    [
        'namespace' => 'Modules\ServiceProvider\Controllers\Site',
        'middleware' => ['logged-in'],

    ],
    function () {
        // list records
        Route::get('/serviceProvider', 'ServiceProviderController@index');
        // one record
        Route::get('/serviceProvider/{id}', 'ServiceProviderController@show');
        // Child routes
    }
);
Route::group([
    'namespace' => 'Modules\ServiceProvider\Controllers\Site',
], function () {
    Route::get('/serviceProviderWebView', 'ServiceProviderController@serviceProviderWebView');
    Route::post('serviceProvider', 'ServiceProviderController@create');
});

Route::group([
    'namespace' => 'Modules\DeliveryMen\Controllers\Site',
], function () {
    Route::get('WebView', 'DeliveryMenController@deliveryMenWebView');
});

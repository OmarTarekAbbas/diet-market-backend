<?php

/*
|--------------------------------------------------------------------------
| Notifications Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Modules\Notifications\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/notifications', 'NotificationsController@index');
    // one record
    Route::delete('/notifications/delete-all', 'NotificationsController@destroyAll');
    Route::delete('/notifications/{id}', 'NotificationsController@destroy');
    Route::patch('/notifications/{id}/mark-as-seen', 'NotificationsController@markAsSeen');
    Route::patch('/notifications/mark-all-as-seen', 'NotificationsController@markAllAsSeen');
    // Child routes
});
 
Route::group([
    'prefix' => '/deliveryMen',
], function () {
    Route::group([
        'namespace' => 'Modules\Notifications\Controllers\Site',
    ], function () {
        Route::group([
            'middleware' => ['logged-in'],
        ], function () {
            Route::get('/notifications', 'NotificationsController@index');
            // one record
            Route::delete('/notifications/delete-all', 'NotificationsController@destroyAll');
            Route::delete('/notifications/{id}', 'NotificationsController@destroy');
            Route::patch('/notifications/{id}/mark-as-seen', 'NotificationsController@markAsSeen');
            Route::patch('/notifications/mark-all-as-seen', 'NotificationsController@markAllAsSeen');
        });
    });
});

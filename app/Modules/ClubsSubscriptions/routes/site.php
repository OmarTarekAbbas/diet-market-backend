<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ClubsSubscriptions Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\ClubsSubscriptions\Controllers\Site',
], function () {
    Route::group([
        'middleware' => ['logged-in'],
    ], function () {
        Route::put('/clubsSubscriptions/status', 'ClubsSubscriptionsController@changeStatus');
        // list records
        Route::get('/clubsSubscriptions', 'ClubsSubscriptionsController@index');
        // one record
        Route::get('/clubsSubscriptions/{id}', 'ClubsSubscriptionsController@show');
        // Child routes
    });
});

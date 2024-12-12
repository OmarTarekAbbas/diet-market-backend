<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ClubBookings Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\ClubBookings\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/clubbookings', 'ClubBookingsController@index');
    // one record
    Route::get('/clubbookings/{id}', 'ClubBookingsController@show');

    Route::post('/clubbookings/store', 'ClubBookingsController@store');

    Route::post('/clubbookings/{clubBookingId}/{status}', 'ClubBookingsController@changeStatus');

    Route::patch('/clubbookings/{clubBookingId}/{status}', 'ClubBookingsController@changeStatus');

    // Child routes

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['restaurantManager'],
    ], function () {
        Route::get('/clubbookings', 'ClubBookingsController@indexClubManagers');
        // one record
        Route::get('/clubbookings/{id}', 'ClubBookingsController@show');

        Route::post('/clubbookings/store', 'ClubBookingsController@storeClubManagers');

        Route::patch('/clubbookings/{clubBookingId}/{status}', 'ClubBookingsController@changeStatus');
    });
});

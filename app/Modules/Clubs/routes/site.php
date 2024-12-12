<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Clubs Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Clubs\Controllers\Site',
], function () {
    // list records
    Route::get('/clubs', 'ClubsController@index');

    Route::get('/clubs/schedule/{id}', 'ClubsController@schedule');
    // one record
    Route::get('/clubs/{id}', 'ClubsController@show');

    // Child routes
    Route::get('/getHealthyDataUser/{id}', 'ClubsController@getHealthyDataUser');

    Route::group(['middleware' => ['logged-in']], function () {
        Route::post('/clubs/reviews', 'ClubReviewsController@store');
    });

    Route::get('/listClubs/reviews', 'ClubReviewsController@index');

    Route::group([
        'prefix' => '/clubManagers',
        // 'middleware' => ['restaurantManager'],
    ], function () {
        Route::get('/my-club', 'ClubsController@getMyClub');
        Route::post('/my-club', 'ClubsController@updateMyClub');
        Route::get('/listCustomerClubId', 'ClubsController@listCustomer');
        Route::get('/getHealthyDataUser/{id}', 'ClubsController@getHealthyDataUser');
    });
});

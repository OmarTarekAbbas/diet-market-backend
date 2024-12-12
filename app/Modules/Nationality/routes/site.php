<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Nationality Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Nationality\Controllers\Site',
    // 'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('deliveryMen/nationality', 'NationalityController@index');
    // one record
    Route::get('deliveryMen/nationality/{id}', 'NationalityController@show');
    // Child routes

    Route::post('/createNationality', 'NationalityController@createNationality');
});

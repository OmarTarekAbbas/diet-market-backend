<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DietTypes Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\DietTypes\Controllers\Site',
], function () {
    // list records
    Route::get('/diet-types', 'DietTypesController@index');
    // one record
    Route::get('/diet-types/{id}', 'DietTypesController@show');
    // Child routes

    Route::group([
        'prefix' => '/nutritionSpecialistMangers',
        // 'middleware' => ['logged-in', 'isStoreManager'],
    ], function () {
        Route::get('/diet-types', 'DietTypesController@index');
        Route::get('/diet-types/{id}', 'DietTypesController@show');
    });
});

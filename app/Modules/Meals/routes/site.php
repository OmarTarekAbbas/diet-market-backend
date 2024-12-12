<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Meals Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Meals\Controllers\Site',
], function () {
    // list records
    Route::get('/meals', 'MealsController@index');
    // one record
    Route::get('/meals/{id}', 'MealsController@show');
    // Child routes
});

Route::group([
    'namespace' => 'Modules\Meals\Controllers\Site',
    'middleware' => ['restaurantManager'],
    'prefix' => 'restaurantManager',
], function () {
    Route::get('meals', 'MealsController@meals');

    Route::post('meals', 'MealsController@create');

    Route::put('meal/{id}', 'MealsController@update');

    Route::DELETE('meal/{id}', 'MealsController@destroy');

    Route::get('/meal/{id}', 'MealsController@show');
});

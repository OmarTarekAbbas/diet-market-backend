<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sizes Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Sizes\Controllers\Site',
], function () {
    // list records
    Route::get('/sizes', 'SizesController@index');
    // one record
    Route::get('/sizes/{id}', 'SizesController@show');
    // Child routes
});


Route::group([
    'namespace' => 'Modules\Sizes\Controllers\Site',
    'middleware' => ['restaurantManager'],
    'prefix' => 'restaurantManager',
], function () {
    Route::get('sizes', 'SizesController@sizes');

    Route::post('sizes', 'SizesController@create');

    Route::put('size/{id}', 'SizesController@update');

    Route::get('/size/{id}', 'SizesController@show');

    Route::DELETE('size/{id}', 'SizesController@destroy');
});

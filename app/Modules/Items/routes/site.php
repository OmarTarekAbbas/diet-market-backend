<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Items Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Items\Controllers\Site',
], function () {
    // list records
    Route::get('/items', 'ItemsController@index');
    // one record
    Route::get('/items/{id}', 'ItemsController@show');
    // Child routes
});

Route::group([
    'namespace' => 'Modules\Items\Controllers\Site',
    'middleware' => ['restaurantManager'],
    'prefix' => 'restaurantManager',
], function () {
    Route::get('items', 'ItemsController@items');

    Route::post('items', 'ItemsController@create');

    Route::put('item/{id}', 'ItemsController@update');

    Route::DELETE('item/{id}', 'ItemsController@destroy');

    Route::get('/item/{id}', 'ItemsController@show');
});

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sections Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Sections\Controllers\Site',
], function () {
    // list records
    Route::get('/sections', 'SectionsController@index');
    // one record
    Route::get('/sections/{id}', 'SectionsController@show');
    // Child routes
});

Route::group([
    'namespace' => 'Modules\Sections\Controllers\Site',
    'middleware' => ['restaurantManager'],
    'prefix' => 'restaurantManager',
], function () {
    Route::get('sections', 'SectionsController@sections');

    Route::post('sections', 'SectionsController@create');

    Route::post('section/{id}', 'SectionsController@update');

    Route::get('section/{id}/delete', 'SectionsController@destroy');

    Route::get('/section/{id}', 'SectionsController@show');
});

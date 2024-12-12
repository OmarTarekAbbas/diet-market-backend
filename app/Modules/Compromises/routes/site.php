<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Compromises Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Compromises\Controllers\Site',
], function () {
    // list records
    Route::get('/compromises', 'CompromisesController@index');
    // one record
    Route::get('/compromises/{id}', 'CompromisesController@show');
    // Child routes
    Route::post('/compromises', 'CompromisesController@store');
});

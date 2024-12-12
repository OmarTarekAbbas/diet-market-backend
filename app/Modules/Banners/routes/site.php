<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Banners Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Banners\Controllers\Site',
], function () {
    // list records
    Route::get('/Banners', 'BannersController@index');
    // one record
    Route::get('/Banners/{id}', 'BannersController@show');
    // Child routes
});

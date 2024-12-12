<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Banks Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Banks\Controllers\Site',
], function () {
    // list records
    Route::get('/banks', 'BanksController@index');
    // one record
    Route::get('/banks/{id}', 'BanksController@show');
    // Child routes

    Route::post('/bankTransfers', 'BankTransferController@store');
});

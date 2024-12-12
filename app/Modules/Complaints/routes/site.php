<?php

/*
|--------------------------------------------------------------------------
| Complaints Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Complaints\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/complaints', 'ComplaintsController@index');
    // one record
    Route::get('/complaints/{id}', 'ComplaintsController@show');
    // Child routes
    Route::post('/complaints', 'ComplaintsController@store');
});

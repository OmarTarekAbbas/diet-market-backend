<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ReceiptRequests Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\ReceiptRequests\Controllers\Site',
], function () {
    // list records
    Route::get('/receiptRequests', 'ReceiptRequestsController@index');
    // one record
    Route::get('/receiptRequests/{id}', 'ReceiptRequestsController@show');
    //receiptRequests/restaurants
    Route::post('/receiptRequests/restaurants', 'ReceiptRequestsController@createReceiptRequestsRestaurants');
    //receiptRequests/home
    Route::post('/receiptRequests/home', 'ReceiptRequestsController@createReceiptRequestsHome');
});

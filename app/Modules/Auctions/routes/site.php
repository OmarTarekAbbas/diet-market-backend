<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auctions Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\Auctions\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/auctions', 'AuctionsController@index');
    // one record
    Route::get('/auctions/{id}', 'AuctionsController@show');
    // Child routes
    // add auction
    Route::post('/auctions', 'AuctionsController@store');
    
    Route::get('/auction/customer-products', 'AuctionsController@getAllProductAuctions');
});

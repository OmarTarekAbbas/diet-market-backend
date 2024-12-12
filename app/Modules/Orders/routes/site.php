<?php

/*
|--------------------------------------------------------------------------
| Orders Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Orders\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    // list records
    Route::get('/orders/canceling-reasons', 'CancelingReasonsController@index');

    Route::get('/orders/returning-reasons', 'ReturningReasonsController@index');
    Route::get('/orders/packaging-status', 'PackagingStatusController@index');
    Route::get('/orders/returned', 'OrdersController@listReturnedOrders');
    Route::get('/orders/returned/{id}', 'OrdersController@listReturnedOrdersId');

    Route::get('/orders', 'OrdersController@index');

    Route::post('/orders', 'OrdersController@store');
    // Route::post('/orders/confirm', 'OrdersController@confirmOnlinePayment');
    Route::post('/orders/{orderId}/rate', 'OrdersController@rate');
    Route::post('/orders/{orderId}/reorder', 'OrdersController@reorder');
    Route::post('/orders/{orderId}/{status}', 'OrdersController@changeStatus');
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus');
    Route::patch('/orders/items/{id}/change-status/{status}', 'OrdersController@changeStatuItems');
    

    // one record
    Route::get('/orders/{id}', 'OrdersController@show');
    // Child routes

    Route::get('/seller/orders', 'OrdersController@sellerOrders');

    Route::get('/orders/successfully', 'OrdersController@orderSuccessfully');
    Route::get('/orders/fail', 'OrdersController@orderFail');

    Route::get('/ordersDelete', 'OrdersController@ordersDelete');
    // Route::get('fixTransaction', function () {
    //     \Artisan::call('php artisan fix:transaction');
    //     dd("done");
    // });
});
Route::get('reviews', 'Modules\Orders\Controllers\Site\ReviewsController@index');

Route::group([
    'namespace' => 'Modules\Orders\Controllers\Admin',
    'prefix' => '/storeManagers',
    'middleware' => ['logged-in', 'isStoreManager'],
], function () {
    Route::apiResource('/orders', 'OrdersController');
    Route::apiResource('/orders/reviews', 'ReviewsController');
    Route::apiResource('/canceling-reasons', 'CancelingReasonsController');
    Route::apiResource('/returning-reasons', 'ReturningReasonsController');
    Route::apiResource('/packaging-status', 'PackagingStatusController');
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus')->name('orders.changeStatus');
    
    Route::patch('/orders/items/{id}/change-status/{status}', 'OrdersController@changeStatuItems');

    Route::get('/ordersReturned', 'OrdersController@listReturnedOrders');
    Route::get('/ordersReturned/{id}', 'OrdersController@listReturnedOrdersId');
    Route::patch('/ordersReturned/{id}/{status}', 'OrdersController@listReturnedOrdersChangeStatus');
    Route::post('/ordersUpdateBoxItemBySeller/{id}', 'OrdersController@updateBoxItemBySeller');
});

Route::group([
    'namespace' => 'Modules\Orders\Controllers\Admin',
    'prefix' => '/restaurantManager',
    // 'middleware' => ['logged-in'],
], function () {
    Route::apiResource('/orders', 'OrdersController');
    Route::apiResource('/orders/reviews', 'ReviewsController');
    Route::apiResource('/canceling-reasons', 'CancelingReasonsController');
    Route::apiResource('/returning-reasons', 'ReturningReasonsController');
    Route::apiResource('/packaging-status', 'PackagingStatusController');
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus')->name('orders.changeStatus');
});

Route::group([
    'namespace' => 'Modules\Orders\Controllers\Admin',
    'prefix' => '/nutritionSpecialistMangers',
    // 'middleware' => ['logged-in'],
], function () {
    Route::apiResource('/orders', 'OrdersController');
    Route::apiResource('/orders/reviews', 'ReviewsController');
    Route::apiResource('/canceling-reasons', 'CancelingReasonsController');
    Route::apiResource('/returning-reasons', 'ReturningReasonsController');
    Route::apiResource('/packaging-status', 'PackagingStatusController');
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus')->name('orders.changeStatus');
});

Route::group([
    'namespace' => 'Modules\Orders\Controllers\Admin',
    'prefix' => '/clubManagers',
    // 'middleware' => ['logged-in'],
], function () {
    Route::apiResource('/orders', 'OrdersController');
    Route::apiResource('/orders/reviews', 'ReviewsController');
    Route::apiResource('/canceling-reasons', 'CancelingReasonsController');
    Route::apiResource('/returning-reasons', 'ReturningReasonsController');
    Route::apiResource('/packaging-status', 'PackagingStatusController');
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus')->name('orders.changeStatus');
});

<?php

/*
|--------------------------------------------------------------------------
| Orders Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Orders\Controllers\Admin',
    'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
], function () {
    // Sub API CRUD routes
    Route::patch('/orders/{orderId}/{status}', 'OrdersController@changeStatus')->name('orders.changeStatus');

    Route::apiResource('/orders/reviews', 'ReviewsController');
    Route::apiResource('/canceling-reasons', 'CancelingReasonsController');
    Route::apiResource('/returning-reasons', 'ReturningReasonsController');
    Route::apiResource('/packaging-status', 'PackagingStatusController');
    Route::apiResource('/deliveryReasons-rejected', 'DeliveryReasonsRejectedController');
    Route::apiResource('/deliveryReasons-notCompleted', 'DeliveryReasonsNotCompletedOrderController');
    Route::get('/ordersReturned', 'OrdersController@listReturnedOrders');
    Route::get('/ordersReturned/{id}', 'OrdersController@listReturnedOrdersId');
    Route::patch('/ordersReturned/{id}/{status}', 'OrdersController@listReturnedOrdersChangeStatus');
    // Main API CRUD routes
    Route::apiResource('/orders', 'OrdersController');
    Route::apiResource('orders-delivery', 'OrderDeliveryController');
    Route::apiResource('/orderStatusDelivery', 'OrderStatusDeliveryController');
    Route::post('manualDeliveryAssignment/{id}', 'OrderDeliveryController@manualDeliveryAssignment');
    Route::patch('/orderDelivery/{orderDeliveryId}/{status}', 'OrderDeliveryController@changeStatus')->name('orders.changeStatus');
    Route::post('/orderUpdateBoxItem/{id}', 'OrdersController@updateBoxItem');

});
// [admin] ChangeStatus Return order

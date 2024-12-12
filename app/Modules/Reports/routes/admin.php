<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reports Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/

Route::group([
    'prefix' => '/reports',
    'namespace' => 'Modules\Reports\Controllers\Admin',
    'middleware' => ['logged-in'], // this middleware is used to check if user/admin is logged in
], function () {
    // Sub API CRUD routes
    // Main API CRUD routes
    Route::group(['prefix' => '/sales'], function () {
        Route::get('byWeek', 'SalesReportsController@weekReport');
        Route::get('byMonth', 'SalesReportsController@monthReport');
        Route::get('byYear', 'SalesReportsController@yearReport');
    });

    Route::get('coupons', 'CouponReportsController@index');
    Route::get('shippingMethods', 'ShippingMethodsReportsController@index');
    Route::get('store/financialReport', 'OrdersReportsController@financialReportStore');
    Route::get('resturant/financialReport', 'OrdersReportsController@financialReportResturant');
    Route::get('club/financialReport', 'OrdersReportsController@financialReportClub');
    Route::get('clinic/financialReport', 'OrdersReportsController@financialReportClinic');

    Route::group(['prefix' => '/products'], function () {
        Route::get('totalViews', 'ProductsReportsController@getTotalViews');
        Route::get('totalSales', 'ProductsReportsController@getTotalSales');
        Route::get('outOfStock', 'ProductsReportsController@getOutOfStock');
    });

    Route::group(['prefix' => '/orders'], function () {
        Route::get('customers', 'OrdersReportsController@index');
    });
});

Route::group([
    'prefix' => '/deliveryMen/reports',
], function () {
    Route::group([
        'namespace' => 'Modules\Reports\Controllers\Admin',
    ], function () {
        Route::group([
            'middleware' => ['logged-in'],
        ], function () {
            Route::get('sales/byWeek', 'DeliveryReportsController@weekReport');
            Route::get('sales/byMonth', 'DeliveryReportsController@monthReport');
            Route::get('sales/byYear', 'DeliveryReportsController@yearReport');

            Route::get('counts/byWeek', 'DeliveryReportsController@weekReportCount');
            Route::get('counts/byMonth', 'DeliveryReportsController@monthReportCount');
            Route::get('counts/byYear', 'DeliveryReportsController@yearReportCount');
        });
    });
});

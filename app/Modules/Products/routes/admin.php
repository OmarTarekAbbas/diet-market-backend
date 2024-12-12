<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Products Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\Products\Controllers\Admin',
    'middleware' => ['authorized'], // this middleware is used to check if user/admin is logged in
    'as' => 'admin.',
], function () {
    // Sub API CRUD routes
    Route::apiResource('/Listproducts/reviews', 'ProductReviewsController');
    Route::apiResource('/productCollections', 'ProductCollectionsController');
    // Main API CRUD routes
    Route::apiResource('/products', 'ProductsController');
    Route::apiResource('/productMeals', 'ProductMealsController');
    Route::apiResource('/productPackageSizes', 'ProductPackageSizeController');
});

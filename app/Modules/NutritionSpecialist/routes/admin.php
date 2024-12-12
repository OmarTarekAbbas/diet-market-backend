<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NutritionSpecialist Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your admin "back office/dashboard" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "admin" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\NutritionSpecialist\Controllers\Admin',
    'middleware' => ['authorized'], // this middleware is used to check if user/admin is logged in
], function () {
    // Sub API CRUD routes
    // Main API CRUD routes
    Route::apiResource('/nutritionspecialist', 'NutritionSpecialistController');

    Route::apiResource('/nutritionSpecialist/reviews', 'NutritionSpecialistReviewsController');
    Route::get('/nutritionSpecialist/schedule/{id}', 'NutritionSpecialistController@schedule');
    Route::get('/getHealthyDataUser/{id}', 'NutritionSpecialistController@getHealthyDataUser');

    Route::apiResource('/nutritionSpecialistNotes', 'NutritionSpecialistNotesController');
    Route::apiResource('/nutritionSpecialistNotesCustomer', 'NutritionSpecialistNotesCustomerController');
});

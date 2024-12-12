<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NutritionSpecialist Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/

Route::group([
    'namespace' => 'Modules\NutritionSpecialist\Controllers\Site',
], function () {
    // list records
    // Route::get('/nutritionSpecialist', 'NutritionSpecialistController@index');
    // // one record
    // Route::get('/nutritionSpecialist/{id}', 'NutritionSpecialistController@show');
    // Child routes

    Route::get('/nutritionSpecialist/schedule/{id}', 'NutritionSpecialistController@schedule');

    Route::get('reviewSchedule', 'NutritionSpecialistController@reviewSchedule');


    Route::group(['middleware' => ['logged-in']], function () {
        Route::post('/nutritionSpecialist/reviews', 'NutritionSpecialistReviewsController@store');
    });

    Route::get('/listnutritionSpecialist/reviews', 'NutritionSpecialistReviewsController@index');


    Route::group([
        'prefix' => '/nutritionSpecialistMangers',
        // 'middleware' => ['restaurantManager'],
    ], function () {
        Route::get('/my-nutrition', 'NutritionSpecialistController@getMyNutrition');
        Route::post('/my-nutrition', 'NutritionSpecialistController@updateMyNutrition');
        Route::get('/nutritionSpecialist/schedule/{id}', 'NutritionSpecialistController@scheduleNutrition');
        Route::get('/getHealthyDataUser/{id}', 'NutritionSpecialistController@getHealthyDataUser');
        Route::apiResource('/nutritionSpecialistNotes', 'NutritionSpecialistNotesController');
    });
});

<?php

/*
|--------------------------------------------------------------------------
| General Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\General\Controllers\Site',
], function () {
    // list records
    Route::get('/about-us', 'AboutUsController@index');
    Route::get('/settings', 'SettingsController@index');
    Route::get('/terms-conditions', 'TermsAndConditions@index');
    Route::get('/privacy-policy', 'PrivacyPolicyController@index');
    Route::get('/returnOrderPolicy', 'SettingsController@returnOrderPolicy');
    // one record
    Route::get('/home', 'HomeController@index');
    Route::get('/checkHealthyData', 'HomeController@checkHealthyData');
    Route::get('/general/{id}', 'GeneralController@show');
    // Child routes

    Route::get('/payment-methods', 'PaymentMethodController@index');

    Route::get('/search', 'SearchController@index');
    Route::group([
        // 'middleware' => ['logged-in'],
    ], function () {
        Route::get('/deliveryMen/terms-ofUse-delivery', 'TermsAndConditionDeliverys@termsOfUseDelivery'); // شروط الاستخدام

        Route::get('/deliveryMen/privacy-policy-delivery', 'TermsAndConditionDeliverys@privacyPolicyDelivery'); //سياسه الخصوصية

        Route::get('/deliveryMen/conditions-working-delivery', 'TermsAndConditionDeliverys@conditionsWorkingDelivery'); //شروط العمل كمندوب
    });
});

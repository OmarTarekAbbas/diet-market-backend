<?php

/*
|--------------------------------------------------------------------------
| ContactUs Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\ContactUs\Controllers\Site',
], function () {
    // Send a support ticket
    Route::get('/info/contact-us', 'ContactUsController@contactUsInfo');
    Route::post('/info/contact-us/submit', 'ContactUsController@submit');
    // Child routes
});

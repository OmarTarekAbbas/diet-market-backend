<?php

/*
|--------------------------------------------------------------------------
| AddressBook Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your main "front office" application.
| Please note that this file is auto imported in the main routes file, so it will inherit the main "prefix"
| and "namespace", so don't edit it to add for example "api" as a prefix.
*/
Route::group([
    'namespace' => 'Modules\AddressBook\Controllers\Site',
    'middleware' => ['logged-in'],
], function () {
    Route::post('/addressBook/{id}/verify/resend', 'AddressBookController@requestVerification');
    Route::post('/addressBook/{id}/verify/{verificationCode}', 'AddressBookController@verify');
    Route::post('/addressBook/{id}/update-phone/verify/{verificationCode}', 'AddressBookController@verifyUpdatedPhoneNumber');
    Route::post('/addressBook/{id}/update-phone', 'AddressBookController@updatePhoneNumber');
    // Resource
    Route::apiResource('/addressBook', 'AddressBookController');
    // Child routes
});

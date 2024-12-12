<?php

use App\Http\Controllers\shippingController;
use App\Modules\Orders\Controllers\Site\OrdersController;
use App\Modules\CustomerGroups\Controllers\Admin\CustomerGroupsController;
use App\Modules\Test\Controllers\Site\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test', [TestController::class, 'index']);

Route::post('/users', function() {
    return request()->all();
});


//Route::get('/testPayment', [TestController::class, 'testPayment'])->name('testPayment');
//Route::get('/testPaymentConfirm', [TestController::class, 'testPaymentConfirm'])->name('testPaymentConfirm');
// Route::post('noonPayment/confirmOnlinePayment', [OrdersController::class, 'confirmOnlinePayment'])->name('noonPayment.returnUrl');
Route::get('/orders/confirm/noon', [OrdersController::class, 'confirmOnlinePayment']);
Route::get('/customersGroups/downloadSample', 'CustomerGroupsController@getDownload');
Route::get('/customersGroups/downloadSample', [CustomerGroupsController::class, 'getDownload']);
Route::get('/fixTransaction', [CustomerGroupsController::class, 'fixTransaction']);
Route::post('/webhooks/shipping', [shippingController::class, 'webhooksShipping']);
Route::get('getFileWebhooks', [shippingController::class, 'getFileWebhooks']);


define('KEY_GOOGLE_MAB', 'AIzaSyBhNrmYXMj0CdXh0hxJmUy-681HzzyVLzo');
define('kbit', '1000');

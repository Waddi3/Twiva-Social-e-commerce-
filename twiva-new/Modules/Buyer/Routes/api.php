<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Buyer\Http\Controllers\Api\V1\ApiCustomerController;
use Modules\Buyer\Http\Controllers\Api\V1\ApiCustomerExpressController;
use Modules\Buyer\Http\Controllers\Api\V1\ApiCustomerInvoiceController;
use Modules\Buyer\Http\Controllers\Api\V1\ApiCustomerRegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/buyer', function (Request $request) {
    return $request->user();
});


Route::domain(config('app.api_url'))->prefix('v1')->group(function () {

    Route::middleware('auth:api')->group(function () {
        Route::post('customer/register', [ApiCustomerRegisterController::class, 'register'])->name('api-create-new-customer-by-user');
        Route::get('customer/send-otp', [ApiCustomerRegisterController::class, 'sendOtp'])->name('api-create-new-customer-by-user-send-otp');
        Route::get('customer/check-otp', [ApiCustomerRegisterController::class, 'checkOtp'])->name('api-create-new-customer-by-user-check-otp');
        Route::get('customer/list', [ApiCustomerController::class, 'customerList'])->name('api-customer-list-for-user');
        Route::get('customer/{id}/show', [ApiCustomerController::class, 'show'])->name('api-customer-show-for-user');
        Route::post('customer/{id}/update', [ApiCustomerController::class, 'update'])->name('api-customer-update-for-user');
        Route::post('customer/{id}/delete', [ApiCustomerController::class, 'delete'])->name('api-customer-delete-for-user');
        // invoice
        Route::get('customer/invoice/list', [ApiCustomerInvoiceController::class, 'list'])->name('api-customer-list-of-invoices');
        Route::get('customer/invoice/{id}/show', [ApiCustomerInvoiceController::class, 'show'])->name('api-customer-show-of-invoices');
        Route::post('customer/invoice/create', [ApiCustomerInvoiceController::class, 'create'])->name('api-customer-create-invoice');
        // express
        Route::get('customer/express/list', [ApiCustomerExpressController::class, 'list'])->name('api-customer-list-of-express');
        Route::get('customer/express/{id}/show', [ApiCustomerExpressController::class, 'show'])->name('api-customer-show-of-express');
        Route::post('customer/express/create', [ApiCustomerExpressController::class, 'create'])->name('api-customer-create-express');
        Route::post('customer/express/create-preview', [ApiCustomerExpressController::class, 'createPreviewLink'])->name('api-customer-create-preview-link-express-preview');
        Route::post('customer/express/{id}/delete', [ApiCustomerExpressController::class, 'delete'])->name('api-customer-delete-express');
    });
});

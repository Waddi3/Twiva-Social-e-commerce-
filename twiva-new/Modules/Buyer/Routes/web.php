<?php

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

use Illuminate\Support\Facades\Route;
use Modules\Buyer\Http\Controllers\Buyer\BuyerExpressController;
use Modules\Buyer\Http\Controllers\Buyer\BuyerInvoiceController;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceOrderController;


Route::prefix('buyer')->group(function () {
    Route::get('/', 'BuyerController@index');
});


Route::get('invoice/{slug}', [BuyerInvoiceController::class, 'checkout'])->name('buyer-invoice-link');

Route::get('/order/{orderId}/download-receipt', [ECommerceOrderController::class, 'downloadReceipt'])->name('buyer-receipt.pdf')->middleware('auth:buyer');

Route::get('express/{slug}', [BuyerExpressController::class, 'checkout'])->name('buyer-express-link');

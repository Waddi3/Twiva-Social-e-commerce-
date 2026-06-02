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
use Modules\Coupon\Http\Controllers\Admin\AdminCouponController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->prefix('coupons')->group(function () {
        Route::get('/', [AdminCouponController::class, 'index'])->name('admin.coupons.index');
        Route::get('activation', [AdminCouponController::class, 'activation'])->name('admin.coupons.activation');
        Route::get('toggle-bypass-the-minimum', [AdminCouponController::class, 'toggleBypassTheMinimum'])->name('admin.coupons.toggle.bypass.the.minimum');
        Route::get('create', [AdminCouponController::class, 'create'])->name('admin.coupons.create');
        Route::post('store', [AdminCouponController::class, 'store'])->name('admin.coupons.store');
        Route::get('{coupon}/edit', [AdminCouponController::class, 'edit'])->name('admin.coupons.edit');
        Route::put('{coupon}/update', [AdminCouponController::class, 'update'])->name('admin.coupons.update');
        Route::delete('{coupon}/destroy', [AdminCouponController::class, 'destroy'])->name('admin.coupons.destroy');
    });
});

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
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Entities\CouponBuyer;
use Modules\Coupon\Http\Controllers\Buyer\BuyerCouponController;
use Modules\Order\Entities\Order;

Route::domain('{account?}' . config('twiva.bases.shop_domain'))->where(['account' => '^(?!.*\bwww\b)(?!.*\badmin\b)(?!.*\bapi\b).*$'])->group(function ($account = null) {
    Route::prefix('coupon')->group(function ($account = null) {
        Route::middleware('web')->group(function ($account = null) {
            Route::get('/', [BuyerCouponController::class, 'checkCoupon'])->name('buyer.check.coupon.validation');
            Route::get('spin-wheel', [BuyerCouponController::class, 'spinWheel'])->name('buyer.spin.wheel.coupon');
        });
    });
});

Route::domain('{account?}' . config('twiva.bases.shop_domain'))->where(['account' => '^(?!.*\bwww\b)(?!.*\badmin\b)(?!.*\bapi\b).*$'])->group(function ($account = null) {
    Route::get('cccccccccc/{id?}', function ($id = null) {
        session()->remove('coupon_code');
        session()->remove('coupon_key');
        dd(session()->all(), cache()->flush());
        // $coupon_buyer = session('coupon_buyer');
        $coupon_buyer = CouponBuyer::find(3);
        dd($coupon_buyer->coupon);
        // dd($coupon_buyer);
        $order = Order::find(680);
        $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order);
        dd(cache()->keys('*'));
        // $arr = '["1", "2", "3"]';
        // dd(json_decode($arr, true));

        $order = Order::with('coupons')->where('id', 669)->first();

        dd($order);


        $coupon = Coupon::with('orders', 'couponOrders')->where('id', $id)->first();
        dd($coupon, $coupon->couponOrders->first()->orders);
        return 'cccccccccc';
    });
});

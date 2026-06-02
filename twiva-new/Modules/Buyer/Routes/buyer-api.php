<?php

use Illuminate\Support\Facades\Route;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\ApiBuyerAuthController;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\Coupon\ApiBuyerCouponController;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\Order\ApiBuyerOrderController;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\Order\ApiBuyerOrderPaymentController;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\Profile\ApiBuyerProfileController;
use Modules\Buyer\Http\Controllers\Api\Buyer\V1\Product\ApiBuyerProductController;
use Modules\Home\Http\Controllers\API\V2\ApiRegionController;

// Route::prefix('v1/{shop_domain}')->group(function () {
//     // auth routes
//     Route::post('register', [ApiBuyerAuthController::class, 'register'])->name('buyer.api.register');
//     Route::post('login', [ApiBuyerAuthController::class, 'login'])->name('buyer.api.login');


//     Route::middleware('auth:buyer-api')->group(function () {
//         Route::get('profile', [ApiBuyerProfileController::class, 'profile'])->name('buyer.api.show.profile');
//     });
// });


Route::domain('{account}.' . config('twiva.bases.shop_domain'))->where(['account' => '^(?!.*\bwww\b)(?!.*\badmin\b)(?!.*\bapi\b).*$'])->group(function ($account) {

    Route::prefix('v1')->middleware(['ApiBuyerCheckSellerInfo'])->group(function () {

        Route::post('register', [ApiBuyerAuthController::class, 'register'])->name('buyer.api.register');
        Route::post('login', [ApiBuyerAuthController::class, 'login'])->name('buyer.api.login');


        // countries | states | cities
        Route::get('country/list', [ApiRegionController::class, 'countryList'])->name('buyer.api.get.list.of.countries');
        Route::get('state/select', [ApiRegionController::class, 'stateSelect'])->name('buyer.api.get.state.select');
        Route::get('city/select', [ApiRegionController::class, 'citySelect'])->name('buyer.api.get.city.select');

        // products
        // Route::get('products/list', [ApiBuyerProductController::class, 'products'])->name('buyer.api.list.products.without.auth');
        // Route::get('products/{id}/details', [ApiBuyerProductController::class, 'show'])->name('buyer.api.show.product.without.auth');

        // products
        Route::get('products', [ApiBuyerProductController::class, 'products'])->name('buyer.api.list.products');
        Route::get('products/{id}/show', [ApiBuyerProductController::class, 'show'])->name('buyer.api.show.product');
        // Hot Deals
        Route::get('hot-deals', [ApiBuyerProductController::class, 'hotDeals'])->name('buyer.api.list.hot.deals');
        // Products might like
        Route::get('products-might-like', [ApiBuyerProductController::class, 'productsMightLike'])->name('buyer.api.list.products.might.like');
        // Featured categories
        Route::get('/featured-categories', [ApiBuyerProductController::class, 'featuredCategories'])->name('buyer.api.categories.suggestion');
        // Get Banner
        Route::get('/get-banner', [ApiBuyerProductController::class, 'getBanner'])->name('get.banner');

        // buyer need to auth and get jwt token by seller
        Route::middleware(['auth:buyer-api', 'BuyerRelateWithShop'])->group(function () {
            //profile
            Route::get('profile', [ApiBuyerProfileController::class, 'profile'])->name('buyer.api.show.profile');
            Route::post('profile', [ApiBuyerProfileController::class, 'updateProfile'])->name('buyer.api.update.profile');
            // profile address
            Route::get('profile/addresses', [ApiBuyerProfileController::class, 'addresses'])->name('buyer.api.show.addresses');
            Route::post('profile/addresses/create', [ApiBuyerProfileController::class, 'createAddress'])->name('buyer.api.create.address');
            Route::post('profile/addresses/{id}', [ApiBuyerProfileController::class, 'updateAddress'])->name('buyer.api.update.address');
            Route::delete('profile/addresses/{id}', [ApiBuyerProfileController::class, 'deleteAddress'])->name('buyer.api.delete.address');
            // order
            Route::get('orders', [ApiBuyerOrderController::class, 'index'])->name('buyer.api.list.orders');
            Route::post('orders/shipping-fees', [ApiBuyerOrderController::class, 'shippingFees'])->name('buyer.api.shipping.fees.order');
            Route::post('orders/has-shipping-fees', [ApiBuyerOrderController::class, 'hasShippingFees'])->name('buyer.api.has.shipping.fees.order');
            Route::post('orders/create', [ApiBuyerOrderController::class, 'create'])->name('buyer.api.create.order');
            Route::get('orders/{id}/show', [ApiBuyerOrderController::class, 'show'])->name('buyer.api.show.order');
            // payment
            Route::post('orders/payment', [ApiBuyerOrderPaymentController::class, 'pay'])->name('buyer.api.payment.order');
            Route::post('orders/check-order-status', [ApiBuyerOrderPaymentController::class, 'checkOrderStatus'])->name('buyer.api.check.payment.status.order');
            Route::post('coupon', [ApiBuyerCouponController::class, 'checkCoupon'])->name('buyer.api.coupon.check');
        });
    });
});

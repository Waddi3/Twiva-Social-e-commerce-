<?php

use Apxcde\OnfonSms\OnfonChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Modules\Notification\Notifications\Buyer\OrderHasBeenDispatchedNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Http\Controllers\OrderDeliveryCodeController;
use Modules\Shop\Http\Controllers\CheckoutController;
use Modules\Shop\Http\Controllers\ShopCartRedirectorController;
use Modules\Shop\Http\Controllers\ShopRedirectorController;

Route::group(['middleware' => ['web', 'sameDomain']], function ()
{

    Route::domain('delivery.' . config('twiva.bases.shop_domain'))->group(function ()
    {
//    Route::prefix('order-delivery')->group(function () {
        Route::get('', [OrderDeliveryCodeController::class, 'insert'])->name('buyer.delivery.code.insert');
        Route::post('/issue', [OrderDeliveryCodeController::class, 'reportIssue'])->name('buyer.delivery.issue');
        Route::get('{delivery_code}', [OrderDeliveryCodeController::class, 'show'])->name('buyer.delivery.code.show.details');
        Route::post('/payment', [OrderDeliveryCodeController::class, 'payment'])->name('buyer.delivery.payment');
        Route::get('/payment/confirm', [OrderDeliveryCodeController::class, 'confirmPayment'])->name('buyer.delivery.payment.confirm');
//        Route::get('{delivery_code}/confirm', [OrderDeliveryCodeController::class, 'confirmShow'])->name('buyer.delivery.code.show.confirm');
        Route::post('{delivery_code}', [OrderDeliveryCodeController::class, 'confirm'])->name('buyer.delivery.code.confirm');
        Route::post('/code/resend', [OrderDeliveryCodeController::class, 'resend'])->name('buyer.delivery.code.resend');
        Route::get('/check-status', [OrderDeliveryCodeController::class, 'checkOrderStatus'])->name('buyer.checkout.order.status');
//    });
    });

    Route::domain('{account}.' . config('twiva.bases.shop_domain'))->where(['account' => '^(?!.*\bwww\b)(?!.*\badmin\b)(?!.*\bapi\b).*$'])
        ->group(function ($account)
        {
            Route::middleware(['web'])->group(function ()
            {
                Route::get('/', [ShopRedirectorController::class, 'index'])->name('shop.product.index');
                Route::get('/products', [ShopRedirectorController::class, 'products'])->name('shop.product.products');
                Route::get('/{product}/show', [ShopRedirectorController::class, 'show'])->name('shop.product.show');

                Route::group([], function ()
                {
                    Route::group(['prefix' => 'products'], function ()
                    {
                        Route::match(['GET', 'POST'], 'order-by', [ShopRedirectorController::class, 'orderBy']);
                        Route::match(['GET', 'POST'], 'search', [ShopRedirectorController::class, 'searchProducts']);
                    });
                    Route::get('{product_id}/BuyNow', [ShopRedirectorController::class, 'buyNow'])->name('shop.product.buyNow');
                    Route::post('/get-card-items', [ShopCartRedirectorController::class, 'index'])->withoutMiddleware('IsBuyerVerified');
                    Route::post('/add-to-card', [ShopCartRedirectorController::class, 'create']);
                    Route::post('/update-card-quantity', [ShopCartRedirectorController::class, 'update']);
                    Route::post('/update-shipping-address', [ShopCartRedirectorController::class, 'updateAddress']);
                    Route::post('/delete-from-card', [ShopCartRedirectorController::class, 'delete']);
                    Route::group(['prefix' => 'checkout', 'middleware' => 'IsBuyerVerified'], function ()
                    {
                        Route::get('account', [CheckoutController::class, 'accountPage'])->name('buyer.checkout.account');
                        Route::get('express-preview/{data}', [CheckoutController::class, 'expressAccountPreview'])
                            ->name('buyer.checkout.account.preview')->withoutMiddleware('IsBuyerVerified');
                        Route::get('shipping', [CheckoutController::class, 'shippingPage'])->name('buyer.checkout.shipping');
                        Route::post('store-shipping-info', [CheckoutController::class, 'storeShippingInfo'])->name('buyer.checkout.store-shipping-info');
                        Route::get('payment', [CheckoutController::class, 'paymentPage'])->name('buyer.checkout.payment');
                        Route::post('pay', [CheckoutController::class, 'pay'])->name('buyer.checkout.pay');
                        Route::get('confirm', [CheckoutController::class, 'confirmPage'])->name('buyer.checkout.confirm');
                        Route::get('check-status', [CheckoutController::class, 'checkOrderStatus']);
                    });
                });
            });
        });
});

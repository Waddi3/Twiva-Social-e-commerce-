<?php

use Illuminate\Support\Facades\Route;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceAddressController;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceAuthController;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceBuyerAccountController;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceBuyerController;
use Modules\Buyer\Http\Controllers\ECommerce\ECommerceOrderController;
use Modules\Home\Entities\City;
use Modules\Home\Entities\State;
use Modules\Shop\Http\Controllers\ECommerce\ECommerceCheckoutController;
use Modules\Shop\Http\Controllers\ECommerce\ECommerceShopCartRedirectorController;
use Modules\Shop\Http\Controllers\ECommerce\ECommerceShopRedirectorController;
use Modules\Shop\Http\Controllers\ECommerce\OrderGroupPayController;


Route::domain(config('twiva.bases.shop_domain'))->middleware(['web'])->group(function ()
{
    Route::get('/login', [ECommerceAuthController::class, 'loginPage'])->name('e-commerce.buyer.login');
    Route::post('/login', [ECommerceBuyerController::class, 'login'])->name('e-commerce.buyer.login.action');

    Route::get('/register', [ECommerceAuthController::class, 'registerPage'])->name('e-commerce.buyer.register');
    Route::post('/register', [ECommerceBuyerController::class, 'register'])->name('e-commerce.buyer.register.action');

    Route::get('/resend-code', [ECommerceBuyerController::class, 'resendCode'])->name('e-commerce.buyer.resend.verify.code');

    Route::get('/verify', [ECommerceAuthController::class, 'verifyPage'])->name('e-commerce.buyer.verify');
    Route::post('/verify', [ECommerceBuyerController::class, 'verify'])->name('e-commerce.buyer.verify.action');

    Route::get('/forget-password', [ECommerceAuthController::class, 'forgetPasswordPage'])->name('e-commerce.buyer.forget_password');
    Route::post('/forget-password', [ECommerceBuyerController::class, 'forgetPasswordAction'])->name('e-commerce.buyer.forget-password.action');

    Route::get('/reset-password/{token}', [ECommerceAuthController::class, 'resetPasswordPage'])->name('e-commerce.buyer.reset-password');
    Route::post('/reset-password', [ECommerceBuyerController::class, 'resetPasswordAction'])->name('e-commerce.buyer.reset-password.action');

    Route::post('/logout', [ECommerceAuthController::class, 'logout'])->name('e-commerce.buyer.logout');

    Route::group(['prefix' => 'account-settings', 'middleware' => ['auth:buyer', 'IsBuyerVerified']], function ()
    {
        Route::get('/', [ECommerceBuyerController::class, 'settingsPage'])->name('e-commerce.buyer.settings-page');
        Route::get('address/', [ECommerceAddressController::class, 'index'])->name('e-commerce.buyer.address.index');
        Route::get('address/create', [ECommerceAddressController::class, 'create'])->name('e-commerce.buyer.address.create');
        Route::get('address/store', [ECommerceAddressController::class, 'store'])->name('e-commerce.buyer.address.store');
        Route::get('address/{shippingAddress}/edit', [ECommerceAddressController::class, 'edit'])->name('e-commerce.buyer.address.edit');
        Route::post('address/{shippingAddress}/update', [ECommerceAddressController::class, 'update'])->name('e-commerce.buyer.address.update');
        Route::delete('address/{shippingAddress}/delete', [ECommerceAddressController::class, 'delete'])->name('e-commerce.buyer.address.delete');
        Route::get('password', [ECommerceBuyerController::class, 'changePasswordPage'])->name('e-commerce.buyer.password-change');
        Route::post('password', [ECommerceBuyerController::class, 'changePassword'])->name('e-commerce.buyer.password-change.post');
        Route::get('account', [ECommerceBuyerAccountController::class, 'account'])->name('e-commerce.buyer.account.index');
        Route::post('account', [ECommerceBuyerAccountController::class, 'update'])->name('e-commerce.buyer.account.update');
    });
    Route::group(['prefix' => 'orders', 'middleware' => ['auth:buyer', 'IsBuyerVerified']], function ()
    {
        Route::get('/', [ECommerceOrderController::class, 'index'])->name('e-commerce.buyer.order.index');
        Route::get('/{order}/show', [ECommerceOrderController::class, 'show'])->name('e-commerce.buyer.order.show');
    });

    Route::get('/getCounties', function ()
    {
        return State::select('id', 'name')->where('country_id', request('country_id'))->orderBy('name')->get();
    })->name('e-commerce.buyer.counties.list');
    Route::get('/getCities', function ()
    {
        return City::select('id', 'name')->where('state_id', request('state_id'))->get();
    })->name('e-commerce.buyer.cities.list');
});


Route::domain(config('twiva.bases.shop_domain'))->group(function ()
{
    Route::middleware(['web'])->group(function ()
    {
        Route::group([], function ()
        {
            Route::get('{product}/show', [ECommerceShopRedirectorController::class, 'show'])->name('e-commerce.shop.product.show');
            Route::get('{product_id}/BuyNow', [ECommerceShopRedirectorController::class, 'buyNow'])->name('e-commerce.shop.product.buyNow'); // DONE
            Route::post('/get-card-items', [ECommerceShopCartRedirectorController::class, 'index'])->withoutMiddleware('IsBuyerVerified');
            Route::post('/add-to-card', [ECommerceShopCartRedirectorController::class, 'create']);
            Route::post('/update-card-quantity', [ECommerceShopCartRedirectorController::class, 'update']);
            Route::post('/update-shipping-address', [ECommerceShopCartRedirectorController::class, 'updateAddress']);
            Route::post('/delete-from-card', [ECommerceShopCartRedirectorController::class, 'delete']);

            Route::group(['prefix' => 'checkout', 'middleware' => 'IsBuyerVerified'], function ()
            {
                Route::get('account', [ECommerceCheckoutController::class, 'accountPage'])->name('e-commerce.buyer.checkout.account'); // done
                // Route::get('express-preview/{data}', [CheckoutController::class, 'expressAccountPreview'])
                //     ->name('e-commerce.buyer.checkout.account.preview')->withoutMiddleware('IsBuyerVerified');
                Route::get('shipping', [ECommerceCheckoutController::class, 'shippingPage'])->name('e-commerce.buyer.checkout.shipping'); // progress
                Route::post('store-shipping-info', [ECommerceCheckoutController::class, 'storeShippingInfo'])->name('e-commerce.buyer.checkout.store-shipping-info');
                Route::get('payment', [ECommerceCheckoutController::class, 'paymentPage'])->name('e-commerce.buyer.checkout.payment');
                Route::post('pay', [ECommerceCheckoutController::class, 'pay'])->name('e-commerce.buyer.checkout.pay');
                Route::get('confirm', [ECommerceCheckoutController::class, 'confirmPage'])->name('e-commerce.buyer.checkout.confirm');
                Route::get('check-status', [ECommerceCheckoutController::class, 'checkOrderStatus']);
            });
        });
        Route::group(['prefix' => 'group-pay','middleware' => 'auth:buyer'], function ()
        {
            Route::get('{uuid}', [OrderGroupPayController::class, 'paymentPage'])->name('group-pay');
            Route::post('{uuid}', [OrderGroupPayController::class, 'pay'])->name('group-pay');
        });
    });
});

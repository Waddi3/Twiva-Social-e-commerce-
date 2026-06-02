<?php


use Illuminate\Support\Facades\Route;
use Modules\Buyer\Http\Controllers\AddressController;
use Modules\Buyer\Http\Controllers\AuthController;
use Modules\Buyer\Http\Controllers\BuyerAccountController;
use Modules\Buyer\Http\Controllers\BuyerController;
use Modules\Home\Entities\City;
use Modules\Home\Entities\State;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Controllers\OrderDeliveryCodeController;
use Modules\Payment\Http\Controllers\PesapalPaymentController;
use Modules\Payment\Http\Controllers\SpotitPaymentController;

Route::domain('{account}.' . config('twiva.bases.shop_domain'))->where(['account' => '^(?!.*www)(?!.*admin).*$'])->group(function ($account) {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('buyer.login');
    Route::post('/login', [BuyerController::class, 'login'])->name('buyer.login.action');

    Route::get('/register', [AuthController::class, 'registerPage'])->name('buyer.register');
    Route::post('/register', [BuyerController::class, 'register'])->name('buyer.register.action');

    Route::get('/resend-code', [BuyerController::class, 'resendCode'])->name('buyer.resend.verify.code');

    Route::get('/verify', [AuthController::class, 'verifyPage'])->name('buyer.verify');
    Route::post('/verify', [BuyerController::class, 'verify'])->name('buyer.verify.action');

    Route::get('/forget-password', [AuthController::class, 'forgetPasswordPage'])->name('buyer.forget_password');
    Route::post('/forget-password', [BuyerController::class, 'forgetPasswordAction'])->name('buyer.forget-password.action');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordPage'])->name('buyer.reset-password');
    Route::post('/reset-password', [BuyerController::class, 'resetPasswordAction'])->name('buyer.reset-password.action');

    Route::post('/logout', [AuthController::class, 'logout'])->name('buyer.logout');

    Route::group(['prefix' => 'account-settings', 'middleware' => ['auth:buyer', 'IsBuyerVerified']], function () {
        Route::get('/', [BuyerController::class, 'settingsPage'])->name('buyer.settings-page');
        Route::get('address/', [AddressController::class, 'index'])->name('buyer.address.index');
        Route::get('address/create', [AddressController::class, 'create'])->name('buyer.address.create');
        Route::get('address/store', [AddressController::class, 'store'])->name('buyer.address.store');
        Route::get('address/{shippingAddress}/edit', [AddressController::class, 'edit'])->name('buyer.address.edit');
        Route::post('address/{shippingAddress}/update', [AddressController::class, 'update'])->name('buyer.address.update');
        Route::delete('address/{shippingAddress}/delete', [AddressController::class, 'delete'])->name('buyer.address.delete');
        Route::get('password', [BuyerController::class, 'changePasswordPage'])->name('buyer.password-change');
        Route::post('password', [BuyerController::class, 'changePassword'])->name('buyer.password-change.post');
        Route::get('account', [BuyerAccountController::class, 'account'])->name('buyer.account.index');
        Route::post('account', [BuyerAccountController::class, 'update'])->name('buyer.account.update');
    });
    Route::group(['prefix' => 'orders', 'middleware' => ['auth:buyer', 'IsBuyerVerified']], function () {
        Route::get('/', [OrderController::class, 'index'])->name('buyer.order.index');
        Route::get('/{order}/show', [OrderController::class, 'show'])->name('buyer.order.show');
    });
    Route::get('/getCounties', function () {
        return State::select('id', 'name')->where('country_id', request('country_id'))->orderBy('name')->get();
    })->name('buyer.counties.list');
    Route::get('/getCities', function () {
        return City::select('id', 'name')->where('state_id', request('state_id'))->get();
    })->name('buyer.cities.list');
});

Route::any('spotit-callback-url', [SpotitPaymentController::class, 'callbackUrl'])->name('spotit-callback-url');
Route::get('spotit-list-of-banks', [SpotitPaymentController::class, 'fetchListOfBanks'])->name('spotit-fetch-list-of-banks');


Route::any('pesapal-callback-url', [PesapalPaymentController::class, 'callbackUrl'])->name('pesapal-callback-url');

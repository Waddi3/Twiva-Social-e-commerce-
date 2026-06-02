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
use Modules\Front\Http\Controllers\ECommerce\ECommerceAuthController;
use Modules\Front\Http\Controllers\ECommerce\ECommerceController;

Route::middleware(['web'])->group(function () {
    Route::get('', [ECommerceController::class, 'index'])->name('ecommerce.index');
    Route::get('/products', [ECommerceController::class, 'shop'])->name('ecommerce.shop');

    Route::get('/login', [ECommerceAuthController::class, 'loginPage'])->name('ecommerce.login');
    Route::post('/login', [ECommerceAuthController::class, 'login'])->name('ecommerce.login.action');

    Route::get('/register', [ECommerceAuthController::class, 'registerPage'])->name('ecommerce.register');
    Route::post('/register', [ECommerceAuthController::class, 'register'])->name('ecommerce.register.action');

    Route::get('/verify', [ECommerceAuthController::class, 'verifyPage'])->name('e-commerce.buyer.verify');
    Route::post('/verify', [ECommerceAuthController::class, 'verify'])->name('e-commerce.buyer.verify.action');

    Route::get('/forgot-password', [ECommerceAuthController::class, 'forgetPasswordPage'])->name('ecommerce.forget_password');
    Route::post('/forgot-password', [ECommerceAuthController::class, 'forgetPasswordAction'])->name('ecommerce.forget_password.action');
});

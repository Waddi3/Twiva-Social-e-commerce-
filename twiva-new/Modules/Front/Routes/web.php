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
use Modules\Front\Http\Controllers\FrontController;

Route::get('/', [FrontController::class, 'home'])->name('home.page');
Route::redirect('/home', '/');
Route::get('services', [FrontController::class, 'services'])->name('home.services');
Route::get('influencer-marketing', [FrontController::class, 'influencerMarketing'])->name('home.influencer-marketing');
Route::get('social-commerce', [FrontController::class, 'socialCommerce'])->name('home.social-commerce');
Route::get('about-us', [FrontController::class, 'about'])->name('home.about');
Route::get('faq', [FrontController::class, 'faq'])->name('home.faq');
Route::get('policy', [FrontController::class, 'policy'])->name('home.policy');
Route::get('terms-conditions', [FrontController::class, 'termsConditions'])->name('home.terms-and-conditions');
Route::get('banned', [FrontController::class, 'banned'])->name('home.banned');




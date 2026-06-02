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
use Modules\Platform\Http\Controllers\API\V2\PlatformController;

Route::domain(config('twiva.bases.shop_domain'))->group(function () {

Route::group(['middleware' => 'AddHeaderAccessToken'], function () {
    Route::get('/social/twitter/auth', [PlatformController::class, 'twitterAuthorize']);
    Route::get('/social/tiktok/auth', [PlatformController::class, 'tiktokAuthorize']);
});
Route::get('/social/twitter/auth/callback', [PlatformController::class, 'twitterAuthorizeCallback']);
Route::get('/social/tiktok/auth/callback', [PlatformController::class, 'tiktokAuthorizeCallback']);

});


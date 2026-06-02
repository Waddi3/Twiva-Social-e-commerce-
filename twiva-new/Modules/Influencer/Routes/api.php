<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\API\V2\Influencer\UserController;
use Modules\Influencer\Http\Controllers\API\V2\ApiInfluenceGuestController;
use Modules\Influencer\Http\Controllers\API\V2\ApiInfluenceOtpController;
use Modules\Influencer\Http\Controllers\API\V2\ApiInfluencePasswordController;
use Modules\Influencer\Http\Controllers\API\V2\ApiInfluencerReferEarnController;
use Modules\Influencer\Http\Controllers\API\V2\InfluenceProfileController;
use Modules\Influencer\Http\Controllers\API\V2\InfluencerCategoryController;
use Modules\Influencer\Http\Controllers\API\V2\InfluencerProductController;
use Modules\Influencer\Http\Controllers\API\V2\InfluencerShopController;
use Modules\Influencer\Http\Controllers\API\V3\AuthController;
use Modules\Influencer\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::prefix('influencer')->middleware('auth:api')->group(function () {
        Route::get('shops/list', [InfluencerShopController::class, 'getListableShops'])->name('api-influence-get-shops-list');
        Route::get('shops/view/products/{shop_id}', [InfluencerShopController::class, 'viewShopProducts'])->name('api-influence-view-shop-products');
        Route::post('shops/edit', [InfluencerShopController::class, 'editShop'])->name('api-influence-eshop-edit');
        Route::get('products/list', [InfluencerProductController::class, 'getListableProducts'])->name('api-influence-get-products-list');
        Route::post('change/password', [InfluenceProfileController::class, 'changePassword'])->name('api-influence-change-password');
        // profile edit
        Route::post('edit/profile', [InfluenceProfileController::class, 'editProfile'])->name('api-influence-edit-profile');

        Route::get('referral-points', [ApiInfluencerReferEarnController::class, 'getReferralPoints'])->name('api-influencer-get-referral-points');
        Route::post('redeem-referral', [ApiInfluencerReferEarnController::class, 'redeemReferral'])->name('api-influencer-get-redeem-referral');

        Route::post('upload-image', [InfluencerProductController::class, 'uploadImage'])->name('api-v2-upload-image');
    });
    Route::get('interests', [InfluencerCategoryController::class, 'getCategories'])->name('api-get-categories-interests');
    Route::post('upload-image', [InfluencerProductController::class, 'uploadImage'])->name('api-influence-products-upload-image');
    Route::get('categories', [InfluencerCategoryController::class, 'categoryListing'])->name('api-influence-categories-list');
    Route::get('categories-subcategory', [InfluencerCategoryController::class, 'categorySubCategoryListing'])->name('api-influence-categories-sub_categories-list');
    Route::get('sub-categories/{id}', [InfluencerCategoryController::class, 'subCategoryListing'])->name('api-influence-categories-get-sub_categories-list');
    Route::get('size-color', [InfluencerCategoryController::class, 'SizeAndColorList'])->name('api-influence-size-color');
    // forgot passwords
    Route::post('forgot-password/otp', [ApiInfluencePasswordController::class, 'forgotPasswordOtp'])->name('api-influence-forgot-password-send-otp');
    Route::post('forgot-password/verify/otp', [ApiInfluencePasswordController::class, 'forgotPasswordOtpVerify'])->name('api-influence-forgot-password-reset');
    Route::post('forgot-password/Otp-verify', [ApiInfluencePasswordController::class, 'OtpVerify'])->name('api-influence-forgot-password-verify-otp');
    // login as guest
    Route::post('shop/isDomainAvailable', [ShopController::class, 'isShopAvailable']);

    Route::match(['get', 'post'], 'login/guest', [ApiInfluenceGuestController::class, 'guest']);
});

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {

    Route::post('influencer/register', [AuthController::class, 'register']);
});

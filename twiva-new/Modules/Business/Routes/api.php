<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\API\V2\ApiBusinessDashboardController;
use Modules\Business\Http\Controllers\API\V2\ApiBusinessProductController;
use Modules\Business\Http\Controllers\API\V2\ApiBusinessProfileController;
use Modules\Business\Http\Controllers\API\V2\ApiBusinessSubscriptionController;
use Modules\Business\Http\Controllers\InfluencerController;
use Modules\Influencer\Http\Controllers\API\V2\InfluenceProfileController;
use Modules\Influencer\Http\Controllers\API\V2\UserInfluenceController;
use Modules\Payment\Http\Controllers\API\V3\Business\EarningsHistoryController;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use Modules\User\Entities\User;

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

    Route::middleware('auth:api')->group(function () {
        Route::get('business/dashboard', [ApiBusinessDashboardController::class, 'dashboard'])->name('api-business-dashboard');
        Route::post('product/inviteStatus', [ApiBusinessProductController::class, 'inviteStatus'])->name('api-business-product-invite-status');
        Route::get('fetch-products', [ApiBusinessProductController::class, 'fetchProducts'])->name('api-business-fetch-products');
        Route::get('business/reports', [ApiBusinessProductController::class, 'reportDetails'])->name('api-business-report-details');

        Route::get('business/order-listing', [EarningsHistoryController::class, 'index'])->name('api-business-get-order-list');

        Route::get('get-business-details', [ApiBusinessProfileController::class, 'getBusinessDetails'])->name('api-business-get-order-details-2');
        Route::match(['post', 'put'], 'edit-business-details', [ApiBusinessProfileController::class, 'editBusinessDetails'])->name('api-business-edit-details');
        Route::match(['post', 'put'], 'edit-business-shipping', [ApiBusinessProfileController::class, 'editBusinessShipping'])->name('api-business-edit-shipping');
        Route::post('add-product', [ApiBusinessProductController::class, 'addProduct'])->name('api-business-add-product');
        Route::get('product/{id}', [ApiBusinessProductController::class, 'productDetail'])->name('api-business-product-detail');
        Route::put('product/{id}', [ApiBusinessProductController::class, 'editProduct'])->name('api-business-edit-product-detail');
        Route::delete('product/{id}', [ApiBusinessProductController::class, 'deleteProduct'])->name('api-business-delete-product');
        Route::put('publish-unpublish', [ApiBusinessProductController::class, 'publishUnpublish'])->name('api-business-published-unpublished');
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('subscriptions/purchase', [ApiBusinessSubscriptionController::class, 'purchaseSubscription'])->name('api-business-subscriptions-purchase');
    });
    Route::group(['prefix' => 'influencer', 'middleware' => 'auth:api'], function () {
        Route::get('/dashboard', [UserInfluenceController::class, 'dashboard'])->name('get.influencer.dashboard');
        Route::get('/details', [InfluenceProfileController::class, 'influencerDetails'])->name('api-get.influencer.details');
    });

    Route::group(['prefix' => 'business/influencer', 'middleware' => 'auth:api'], function () {
        Route::get('index', [InfluencerController::class, 'index'])->name('api-business.influencer.index');
        Route::get('show', [InfluencerController::class, 'show'])->name('api-business.influencer.show');
        Route::get('compare', [InfluencerController::class, 'compare'])->name('api-business.influencer.compare');
    });
//    Route::group(['prefix' => 'v2/'], function () {
    Route::get('get-tires', function () {
        return response()->json(['data' => UserPlatform::Tires]);
    });
    Route::get('get-age-ranges', function () {
        return response()->json(['data' => User::ageRanges]);
    });
    Route::get('get-platforms', function () {
        return response()->json(['data' => Platform::select('id', 'name')->get()]);
    });
//    });
});

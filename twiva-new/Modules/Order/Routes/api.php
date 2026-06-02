<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\API\V1\Influencer\InfluencerOrderController;
use Modules\Order\Http\Controllers\API\V2\ApiOrderController;
use Modules\Order\Http\Controllers\API\V3\Business\OrderController;
use Modules\Order\Http\Controllers\API\V3\Business\ReportController;
use Modules\Order\Http\Controllers\TentativeDeliveryPeriodController;
use Modules\Payment\Http\Controllers\API\V3\Influencer\EarningsHistoryController;

Route::domain(config('app.api_url'))->prefix('v1')->middleware('auth:api')->group(function () {

    Route::group(['middleware' => 'auth:api', 'prefix' => 'business/orders'], function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/show', [OrderController::class, 'show']);
        Route::post('/cancel', [OrderController::class, 'cancel']);
        Route::post('/move-order-to-next-status', [OrderController::class, 'moveToNextStatus']);
        Route::get('/tentative-delivery-period-list', [TentativeDeliveryPeriodController::class, 'index']);
    });


    Route::prefix('influencer')->group(function () {
        Route::get('earning-history/shop', [EarningsHistoryController::class, 'indexOrderEarnings'])->name('api-influencer-get-earning-history');
    });
    Route::group(['middleware' => 'auth:api', 'prefix' => 'business/orders/report'], function () {
        Route::get('/monthly', [ReportController::class, 'monthlyReport']);
        Route::get('/yearly', [ReportController::class, 'yearlyReport']);
    });

    Route::group(['prefix' => 'influencer/orders'], function () {
        Route::get('', [InfluencerOrderController::class, 'index']);
        Route::get('show', [InfluencerOrderController::class, 'show']);
    });
});

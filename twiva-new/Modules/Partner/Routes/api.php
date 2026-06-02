<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\API\V3\Business\OrderController;
use Modules\Partner\Http\Controllers\API\V3\PartnerDashboardController;
use Modules\Partner\Http\Controllers\API\V3\PartnerEarningsController;
use Modules\Partner\Http\Controllers\API\V3\PartnerProductController;

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
    Route::group(['middleware' => 'auth:api', 'prefix' => 'partner/orders'], function () {
        Route::get('/', [PartnerDashboardController::class, 'orders']);
        Route::get('/show', [PartnerDashboardController::class, 'show']);
        Route::get('/export', [PartnerDashboardController::class, 'export']);
        Route::post('/status/update', [PartnerDashboardController::class, 'updateStatus']);
        Route::post('/move-order-to-next-status', [PartnerDashboardController::class, 'moveToNextStatus']);
    });

    Route::group(['middleware' => 'auth:api', 'prefix' => 'partner'], function () {
        Route::get('/earning-history', [PartnerEarningsController::class, 'history']);

        Route::group(['prefix' => 'products'], function() {
            Route::get('/', [PartnerProductController::class, 'index']);
            Route::get('show', [PartnerProductController::class, 'show']);
        });
    });
});

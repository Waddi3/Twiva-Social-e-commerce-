<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\API\V2\ApiBankController;
use Modules\Payment\Http\Controllers\API\V3\Business\BankController as BusinessBankController;
use Modules\Payment\Http\Controllers\API\V3\Business\EarningsHistoryController as BusinessEarningsHistoryController;
use Modules\Payment\Http\Controllers\API\V3\Business\PaymentHistoryController as BusinessPaymentHistoryController;
use Modules\Payment\Http\Controllers\API\V3\FlutterWaveController;
use Modules\Payment\Http\Controllers\API\V3\Influencer\BankController as InfluencerBankController;
use Modules\Payment\Http\Controllers\API\V3\Influencer\EarningsHistoryController as InfluencerEarningsHistoryController;
use Modules\Payment\Http\Controllers\API\V3\Influencer\PaymentHistoryController as InfluencerPaymentHistoryController;
use Modules\Payment\Http\Controllers\API\V3\MPESAB2CController;
use Modules\Payment\Http\Controllers\API\V3\MPESAController;
use Modules\Payment\Http\Controllers\API\V3\MPESATransactionController;
use Modules\Payment\Http\Controllers\API\V3\PaymentController;
use Modules\Payment\Http\Controllers\PayoutRequestController;

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

Route::domain(config('app.api_url'))->prefix('v1')->middleware('auth:api')->group(function () {
    Route::post('busi-mpesa', [ApiBankController::class, 'BusiMpesa'])->name('api-busi-mpesa');
    Route::get('busi-mpesa-get', [ApiBankController::class, 'BusiMpesaGet'])->name('api-busi-mpesa-get');
    Route::prefix('influencer')->group(function () {
        Route::get('bank/details', [ApiBankController::class, 'bankDetails'])->name('api-influencer-bank-details');
        Route::get('bank/list', [ApiBankController::class, 'bankList'])->name('api-influencer-banks-list');
        Route::post('bank/setup', [ApiBankController::class, 'bankSetup'])->name('api-influencer-bank-setup');
    });
});


Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::group(['prefix' => 'business/', 'middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'payment-history', 'middleware' => 'auth:api'], function () {
            Route::get('/', [BusinessPaymentHistoryController::class, 'index']);
        });
        Route::group(['prefix' => 'earning-history', 'middleware' => 'auth:api'], function () {
            Route::get('/', [BusinessEarningsHistoryController::class, 'index']);
        });
        Route::group(['prefix' => 'bank', 'middleware' => 'auth:api'], function () {
            Route::get('/show', [BusinessBankController::class, 'show']);
            Route::post('/modifiy', [BusinessBankController::class, 'modify']);
        });
    });

    Route::group(['prefix' => 'influencer/', 'middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'payment-history', 'middleware' => 'auth:api'], function () {
            Route::get('/', [InfluencerPaymentHistoryController::class, 'index']);
        });
        Route::group(['prefix' => 'earning-history', 'middleware' => 'auth:api'], function () {
            Route::get('/campaign', [InfluencerEarningsHistoryController::class, 'indexCampaignEarnings']);
        });
        Route::group(['prefix' => 'bank', 'middleware' => 'auth:api'], function () {
            Route::get('/show', [InfluencerBankController::class, 'show']);
            Route::post('/modifiy', [InfluencerBankController::class, 'modify']);
        });
    });

    Route::prefix('request-payouts')->middleware(['auth:api'])->group(function () {
        Route::post('store', [PayoutRequestController::class, 'store'])->name('api.payout.request.store');
    });

    Route::group(['prefix' => 'payment/', 'middleware' => 'auth:api'], function () {
        Route::post('check-payment-status', [PaymentController::class, 'show']);
        Route::group(['prefix' => 'flutterwave'], function () {
            Route::post('create-transaction', [FlutterWaveController::class, 'createTransaction']);
            Route::post('create-web-transaction', [FlutterWaveController::class, 'createWebTransaction']);
            Route::get('callback', [FlutterWaveController::class, 'callback'])
                ->name('flutterwave.callback')
                ->withoutMiddleware('auth:api');
            Route::get('web-campaign-callback', [FlutterWaveController::class, 'webCampaignCallback'])
                ->name('flutterwave.web-campaign-Callback')
                ->withoutMiddleware('auth:api');
        });
        Route::group(['prefix' => 'mpesa'], function () {
            Route::post('create-transaction', [MPESAController::class, 'createTransaction']);
            Route::post('callback', [MPESAController::class, 'callback'])
                ->name('mpesa.callback')
                ->withoutMiddleware('auth:api');

            Route::any('b2c/queue', [MPESAB2CController::class, 'queue'])
                ->name('mpesa.b2c.queue.callback')
                ->withoutMiddleware('auth:api');
            Route::any('b2c/result', [MPESAB2CController::class, 'result'])
                ->name('mpesa.b2c.result.callback')
                ->withoutMiddleware('auth:api');
            // check-transaction-status
            Route::any('check-transaction-status/queue', [MPESATransactionController::class, 'queue'])
                ->name('mpesa.check-transaction-status.queue.callback')
                ->withoutMiddleware('auth:api');
            Route::any('check-transaction-status/result', [MPESATransactionController::class, 'result'])
                ->name('mpesa.check-transaction-status.result.callback')
                ->withoutMiddleware('auth:api');
        });

        Route::group(['prefix' => 'mp'], function () {
            Route::any('queue', function(){logger('register', ['request' => request()->all()]);})
                ->name('mp.queue.callback')
                ->withoutMiddleware('auth:api');
            Route::any('result', function(){logger('register', ['request' => request()->all()]);})
                ->name('mp.result.callback')
                ->withoutMiddleware('auth:api');
        });
    });
});

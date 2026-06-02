<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\API\V3\Business\Brand\ProductBrandController;
use Modules\Products\Http\Controllers\API\V3\Business\ProductsController;
use Modules\Products\Http\Controllers\API\V3\Influencer\ProductsController as InfluencerProductsController;
use Modules\Products\Http\Controllers\API\V3\ProductCaptionAiController;

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
    Route::group(['prefix' => 'business/products', 'middleware' => 'auth:api'], function () {
        Route::get('/', [ProductsController::class, 'index']);
        Route::get('/show', [ProductsController::class, 'show']);
        Route::post('/store', [ProductsController::class, 'store']);
        Route::post('/update', [ProductsController::class, 'update']);
        Route::post('/delete', [ProductsController::class, 'delete']);
        Route::post('/delete-image-from-product', [ProductsController::class, 'deleteImageFromProduct']);
        Route::post('/toggle-status', [ProductsController::class, 'toggleProductStatus']);
    });
    Route::group(['prefix' => 'influencer/products', 'middleware' => 'auth:api'], function () {
        Route::get('/get-images', [InfluencerProductsController::class, 'getProductImages']);
    });

    Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function () {
        Route::get('/get-images', [InfluencerProductsController::class, 'getProductImages']);
        Route::get('brands/suggestions', [ProductBrandController::class, 'suggestions']);
        Route::get('caption/suggestion', [ProductCaptionAiController::class, 'suggestion']);
    });

});

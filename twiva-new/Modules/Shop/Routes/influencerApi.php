<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\API\V3\Influencer\ProductController;
use Modules\Shop\Http\Controllers\API\V3\Influencer\ShopController;
use Modules\Shop\Http\Controllers\API\V3\Influencer\ShopProductController;


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
    Route::group(['prefix' => 'influencer/shop', 'middleware' => 'auth:api'], function () {

        Route::get('show', [ShopController::class, 'show']);
        Route::post('edit', [ShopController::class, 'edit']);
        Route::post('delete-background-image', [ShopController::class, 'deleteBackgroundImage']);
        Route::post('get-preview-link', [ShopController::class, 'preparePreviewLink']);

        Route::group(['prefix' => 'products-to-add'], function () {
            Route::get('index', [ProductController::class, 'index']);
            Route::get('show', [ProductController::class, 'show']);
        });
        Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function () {
            Route::get('index', [ShopProductController::class, 'index']);
            Route::get('show', [ShopProductController::class, 'show']);
            Route::post('store', [ShopProductController::class, 'store']);
            Route::post('update', [ShopProductController::class, 'update']);
            Route::post('delete-product', [ShopProductController::class, 'deleteProduct']);
            Route::post('delete-image', [ShopProductController::class, 'deleteImage']);
        });
    });

});
Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::group(['prefix' => 'business/shop', 'middleware' => 'auth:api'], function () {

        Route::get('show', [ShopController::class, 'show']);
        Route::post('edit', [ShopController::class, 'edit']);
        Route::post('delete-background-image', [ShopController::class, 'deleteBackgroundImage']);
        Route::post('get-preview-link', [ShopController::class, 'preparePreviewLink']);

        Route::group(['prefix' => 'products-to-add'], function () {
            Route::get('index', [ProductController::class, 'index']);
            Route::get('show', [ProductController::class, 'show']);
        });
        Route::group(['prefix' => 'products', 'middleware' => 'auth:api'], function () {
            Route::get('index', [ShopProductController::class, 'index']);
            Route::get('show', [ShopProductController::class, 'show']);
            Route::post('store', [ShopProductController::class, 'store']);
            Route::post('update', [ShopProductController::class, 'update']);
            Route::post('delete-product', [ShopProductController::class, 'deleteProduct']);
            Route::post('delete-image', [ShopProductController::class, 'deleteImage']);
        });
    });

});

Route::domain(config('app.api_url'))->group(function () {
    Route::get('api/v2/influencer/products/export/{influencer_id}', [ProductController::class, 'exportCSV'])->name('influencer.product.exportCSV');
});



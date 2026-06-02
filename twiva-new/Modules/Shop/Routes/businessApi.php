<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\API\V3\Business\ProductController;
use Modules\Shop\Http\Controllers\API\V3\Business\ShopController;


Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::group(['prefix' => 'business/shop', 'middleware' => 'auth:api'], function () {
        Route::get('show', [ShopController::class, 'show']);
        Route::post('edit', [ShopController::class, 'edit']);
        Route::post('delete-background-image', [ShopController::class, 'deleteBackgroundImage']);
        Route::post('get-preview-link', [ShopController::class, 'preparePreviewLink']);
    });
});
Route::domain(config('app.api_url'))->group(function () {
    Route::get('api/v2/business/products/export/{business_id}', [ProductController::class, 'exportCSV'])->name('business.product.exportCSV');
});




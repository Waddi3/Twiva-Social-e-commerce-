<?php



use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ECommerce\ECommerceCategoryController;
use Modules\Products\Http\Controllers\ECommerce\ECommerceProductController;

Route::domain('{account?}' . config('twiva.bases.shop_domain'))
    ->where(['account' => '^(?!.*\bwww\b)(?!.*\badmin\b)(?!.*\bapi\b).*$'])
    ->group(function ($account = null) {
        Route::prefix('e-commerce')->group(function () {
            Route::get('/featured-categories', [ECommerceCategoryController::class, 'featured'])->name('e-commerce-categories-featured');
            Route::get('/categories-suggestions', [ECommerceCategoryController::class, 'suggestions'])->name('e-commerce-categories-suggestion');
            Route::get('/categories', [ECommerceCategoryController::class, 'getCategoryWithSubcategory'])->name('e-commerce-categories-with-subcategories');
            Route::get('/products/hot-deals', [ECommerceProductController::class, 'hotDeals'])->name('e-commerce-index-products-hot-deals');
            Route::get('/products/trending', [ECommerceProductController::class, 'trendingProducts'])->name('e-commerce-index-products-trending-products');
            Route::get('/products', [ECommerceProductController::class, 'index'])->name('e-commerce-index-products');
            Route::get('/products/new-algorithm', [ECommerceProductController::class, 'indexNewAlgorithm'])->name('e-commerce-index-products-new-algorithm');
            Route::get('/products/hot-deals/new-algorithm', [ECommerceProductController::class, 'hotDealsNewAlgorithm'])->name('e-commerce-products-hot-deals-new-algorithm');
            Route::get('/products/trending/new-algorithm', [ECommerceProductController::class, 'trendingAlgorithm'])->name('e-commerce-trending-products-new-algorithm');
        });
    });

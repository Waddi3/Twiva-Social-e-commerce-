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
use Modules\Admin\Http\Controllers\Admin\Category\AdminCategoryController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->group(function () {
        // Categories
        Route::get('categories', [AdminCategoryController::class, 'index'])->name('admin.category.index');
        Route::get('categories/activation', [AdminCategoryController::class, 'activation'])->name('admin.category.activation');
        Route::get('categories/featured', [AdminCategoryController::class, 'featured'])->name('admin.category.featured');
        Route::get('categories/{id}/edit', [AdminCategoryController::class, 'editPage'])->name('admin.category.edit');
        Route::get('categories/create', [AdminCategoryController::class, 'createPage'])->name('admin.category.create');
        Route::post('categories/create', [AdminCategoryController::class, 'create'])->name('admin.category.create.action');
        Route::POST('categories/update', [AdminCategoryController::class, 'update'])->name('admin.category.update');
        // Sub Categories
        Route::get('sub-categories', [AdminCategoryController::class, 'subCategories'])->name('admin.subcategory.index');
        Route::get('sub-categories/activation', [AdminCategoryController::class, 'subActivation'])->name('admin.subcategory.activation');
        Route::get('sub-categories/create', [AdminCategoryController::class, 'subCreatePage'])->name('admin.subcategory.create');
        Route::post('sub-categories/create', [AdminCategoryController::class, 'subCreate'])->name('admin.subcategory.create.action');
        Route::get('sub-categories/{id}/edit', [AdminCategoryController::class, 'subEditPage'])->name('admin.subcategory.edit');
        Route::POST('sub-categories/update', [AdminCategoryController::class, 'subUpdate'])->name('admin.subcategory.update');
    });
});

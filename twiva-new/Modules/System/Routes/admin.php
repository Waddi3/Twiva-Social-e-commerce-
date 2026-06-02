<?php


use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\Admin\AdminConstantSystemController;
use Modules\System\Http\Controllers\Admin\AdminMobileVersionController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->group(function () {
        Route::prefix('mobile-versions')->group(function () {
            //        Route::get('', [AdminMobileVersionController::class, 'updateIndex'])->name('admin.mobile.version.update.index');
            Route::put('', [AdminMobileVersionController::class, 'updateIndexDB'])->name('admin.mobile.version.update.index.db');
            //        Route::get('index', [AdminMobileVersionController::class, 'index'])->name('admin.mobile.version.index');
            //        Route::get('create', [AdminMobileVersionController::class, 'create'])->name('admin.mobile.version.create');
            //        Route::post('store', [AdminMobileVersionController::class, 'store'])->name('admin.mobile.version.store');
            //        Route::get('change-forced', [AdminMobileVersionController::class, 'changeForced'])->name('admin.mobile.version.change.forced');
            //        Route::get('{mobile_version}/edit', [AdminMobileVersionController::class, 'edit'])->name('admin.mobile.version.edit');
            //        Route::put('{mobile_version}', [AdminMobileVersionController::class, 'update'])->name('admin.mobile.version.update');
        });
        Route::post('update-banner', [AdminConstantSystemController::class, 'updateBanner'])->name('admin.update.banner.e-commerce');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\Admin\AdminNotificationController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->group(function () {
//        Route::get('notifications/list-all-titles', [AdminNotificationController::class, 'index'])->name('admin.notification.list.titles');
        Route::post('notifications/list-all-titles', [AdminNotificationController::class, 'update'])->name('admin.notification.list.titles.update');
    });
});

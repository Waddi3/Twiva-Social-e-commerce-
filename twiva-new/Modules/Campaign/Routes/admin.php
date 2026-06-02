<?php

use Illuminate\Support\Facades\Route;
use Modules\Campaign\Http\Controllers\Admin\AdminCampaignController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->group(function () {
        Route::get('campaigns', [AdminCampaignController::class, 'index'])->name('admin.campaign.index');
        Route::post('campaigns/approve_status', [AdminCampaignController::class, 'changeApproveStatus'])->name('admin.campaign.approve_status');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Campaign\Http\Controllers\API\V3\Business\ApiWhatsAppGroupBusinessCampaignController;

Route::domain(config('app.api_url'))->prefix('v2')->middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'business/whatsapp-groups', 'middleware' => 'auth:api'], function () {
        Route::get('list', [ApiWhatsAppGroupBusinessCampaignController::class, 'listOfWhatsAppGroup'])->name('api-business.whatsapp-groups.index');
        // Route::get('show', [InfluencerController::class, 'show'])->name('api-business.influencer.show');
        // Route::get('compare', [InfluencerController::class, 'compare'])->name('api-business.influencer.compare');
    });
    // Route::group(['prefix' => 'business/campaign/', 'middleware' => 'auth:api'], function () {
    // });
});

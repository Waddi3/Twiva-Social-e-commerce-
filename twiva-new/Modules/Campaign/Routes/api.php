<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\API\V3\AuthController;
use Modules\Campaign\Http\Controllers\API\V3\Business\CampaignController as BusinessCampaignController;
use Modules\Campaign\Http\Controllers\API\V3\Influencer\InvitationController;

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {

    Route::group(['prefix' => 'business/campaign/', 'middleware' => 'auth:api'], function () {
        Route::get('/', [BusinessCampaignController::class, 'index']);
        Route::get('{post_id}/show', [BusinessCampaignController::class, 'show']);
        Route::post('create', [BusinessCampaignController::class, 'create']);
        Route::post('hire', [BusinessCampaignController::class, 'hire']);
        Route::post('cancel', [BusinessCampaignController::class, 'cancel']);

        Route::post('check-waive-prompt', [BusinessCampaignController::class, 'checkWaivePrompt'])->name('api.business.hire.waive.prompt');
    });
    Route::group(['prefix' => 'influencer/invitations', 'middleware' => 'auth:api'], function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::get('show', [InvitationController::class, 'show']);
        Route::post('apply-invitation', [InvitationController::class, 'applyInvitation']);
        Route::post('decline-invitation', [InvitationController::class, 'declineInvitation']);
        // add new structure broadcasts to invitation influencer
        Route::get('/broadcasts', [InvitationController::class, 'indexBroadcast']);
        Route::get('broadcasts/show', [InvitationController::class, 'showBroadcast']);
        Route::post('broadcasts/apply-invitation', [InvitationController::class, 'applyInvitationBroadcast']);
    });
    Route::group(['prefix' => 'business/'], function () {
        Route::post('/register', [AuthController::class, 'register']);
    });
});

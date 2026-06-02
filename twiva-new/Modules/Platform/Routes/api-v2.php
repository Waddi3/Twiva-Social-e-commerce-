<?php

use Illuminate\Support\Facades\Route;
use Modules\Platform\Http\Controllers\API\V2\ApiV2LMMPlatformController;
use Modules\Platform\Http\Controllers\API\V2\ApiV2TikTokPlatformController;
use Modules\Platform\Http\Controllers\API\V2\ApiV2StoryPostContentController;
use Modules\Platform\Http\Controllers\API\V2\ApiV2StoryPostController;
use Modules\Platform\Http\Controllers\API\V2\PlatformController;
use Modules\User\Http\Controllers\API\V2\ApiV2BrandThemeController;

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

Route::domain(config('app.api_url'))->prefix('v2')->group(function () {
    Route::group(['middleware' => 'auth:api', 'prefix' => 'influencer/platforms'], function () {
        Route::get('/', [PlatformController::class, 'index'])->name('api-index-influencer-platforms');
    });
    Route::middleware('auth:api')
        ->prefix('brand-themes')
        ->group(function () {
            Route::get('/', [ApiV2BrandThemeController::class, 'index'])->name('api.v2.brand.themes');
            Route::post('/', [ApiV2BrandThemeController::class, 'update'])->name('api.v2.brand.themes.update');
            Route::get('/languages', [ApiV2BrandThemeController::class, 'languages'])->name('api.v2.brand.themes.languages');
            Route::get('/interests', [ApiV2BrandThemeController::class, 'interests'])->name('api.v2.brand.themes.interests');
        });
    Route::middleware('auth:api')
        ->prefix('platforms')
        ->group(function () {
            Route::get('/', [PlatformController::class, 'index'])->name('api-v2-index-platforms');
            Route::get('/twitter/authorize', [PlatformController::class, 'twitterAuthorize']);
            Route::get('/twitter/authorize/callback', [PlatformController::class, 'twitterAuthorizeCallback']);
            Route::get('/tiktok/authorize', [PlatformController::class, 'twitterAuthorize']);
            Route::get('/tiktok/authorize/callback', [PlatformController::class, 'twitterAuthorizeCallback']);
            // TikTok
            Route::get('/get-tiktok-accounts', [ApiV2TikTokPlatformController::class, 'index']);
            // suggestion caption from chat gpt
            Route::get('/get-caption-suggestion/{product_id}', [ApiV2LMMPlatformController::class, 'generator']);


            Route::prefix('story-posts')
                ->group(function () {
                    Route::get('/', [ApiV2StoryPostController::class, 'index'])->name('api-v2-list-story-posts-platform');
                    Route::get('/show', [ApiV2StoryPostController::class, 'show'])->name('api-v2-show-story-post-platform');
                    Route::post('/create', [ApiV2StoryPostController::class, 'create'])->name('api-v2-create-story-post-platform');
                    Route::post('/cancel', [ApiV2StoryPostController::class, 'cancel'])->name('api-v2-cancel-story-post-platform');
                });

            Route::prefix('generate-video')->group(function () {
                Route::get('/{product_id}', [ApiV2StoryPostContentController::class, 'generateVideo'])->name('api-v2-platform-generate-video');
            });
        });
});
    Route::get('generate-video/{product_id}', [ApiV2StoryPostContentController::class, 'generateVideo'])->name('api-v2-platform-generate-video');

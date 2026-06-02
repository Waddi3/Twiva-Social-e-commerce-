<?php

use Illuminate\Support\Facades\Route;
use Modules\Platform\Http\Controllers\API\V1\PlatformController;
use Modules\Platform\Http\Controllers\API\V2\PlatformController as NewPlatformController;
use Modules\Platform\Http\Controllers\API\V1\StoryPostController;

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
// Route::domain(config('app.api_url'))->prefix('v2')->group(function () {
//     Route::group(['middleware' => 'auth:api', 'prefix' => 'influencer/platforms'], function () {
//         Route::get('/', [NewPlatformController::class, 'index'])->name('api-index-influencer-platforms');
//     });
//     Route::group(['middleware' => 'auth:api', 'prefix' => 'platforms'], function () {
//         Route::get('/', [NewPlatformController::class, 'index'])->name('api-v2-index-platforms');
//         Route::get('/twitter/authorize', [NewPlatformController::class, 'twitterAuthorize']);
//         Route::get('/twitter/authorize/callback', [NewPlatformController::class, 'twitterAuthorizeCallback']);
//         Route::get('/tiktok/authorize', [NewPlatformController::class, 'twitterAuthorize']);
//         Route::get('/tiktok/authorize/callback', [NewPlatformController::class, 'twitterAuthorizeCallback']);
//     });
// });

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {


    Route::group(['middleware' => 'auth:api', 'prefix' => 'influencer/platforms'], function () {
        Route::get('/', [PlatformController::class, 'index'])->name('api-index-influencer-platforms-2');
        Route::post('connect', [PlatformController::class, 'create'])->name('api-create-platforms-contact');
        Route::group(['prefix' => 'story-posts'], function () {
            Route::get('/', [StoryPostController::class, 'index']);
            Route::get('/show', [StoryPostController::class, 'show']);
            Route::post('/create', [StoryPostController::class, 'create']);
            Route::post('/cancel', [StoryPostController::class, 'cancel']);
        });
    });

    Route::group(['middleware' => 'auth:api', 'prefix' => 'platforms'], function () {
        Route::get('/', [PlatformController::class, 'index'])->name('api-index-platforms');
        Route::post('connect', [PlatformController::class, 'create'])->name('api-platforms-create-connection');
        Route::group(['prefix' => 'story-posts'], function () {
            Route::get('/', [StoryPostController::class, 'index']);
            Route::get('/show', [StoryPostController::class, 'show']);
            Route::post('/create', [StoryPostController::class, 'create']);
            Route::post('/cancel', [StoryPostController::class, 'cancel']);
        });
    });

});

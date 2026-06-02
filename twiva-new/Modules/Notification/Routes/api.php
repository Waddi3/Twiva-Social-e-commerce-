<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\V3\NotificationController;

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

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::group(['prefix' => 'influencer/notifications', 'middleware' => 'auth:api'], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('mark-all-as-read', [NotificationController::class, 'markAllNotificationsAsRead']);
        Route::post('mark-as-read', [NotificationController::class, 'markNotificationAsRead']);
    });
    Route::group(['prefix' => 'business/notifications', 'middleware' => 'auth:api'], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('mark-all-as-read', [NotificationController::class, 'markAllNotificationsAsRead']);
        Route::post('mark-as-read', [NotificationController::class, 'markNotificationAsRead']);
    });

});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\API\V2\ApiNotificationController;
use Modules\Home\Http\Controllers\API\V2\ApiRegionController;

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

Route::middleware('auth:api')->get('/home', function (Request $request) {
    return $request->user();
});


Route::domain(config('app.api_url'))->prefix('v1')->group(function () {
    Route::get('country/list', [ApiRegionController::class, 'countryList'])->name('api-get-list-of-countries');
//    Route::get('country-code/list', [ApiRegionController::class, 'getCountryCodeList'])->name('api-get-list-of-countries-code');
    Route::get('state/select', [ApiRegionController::class, 'stateSelect'])->name('api-get-state-select');
    Route::get('city/select', [ApiRegionController::class, 'citySelect'])->name('api-get-city-select');
    Route::get('city/country', [ApiRegionController::class, 'cityCountry'])->name('api-get-city-country');
    Route::get('business/notifications/listing', [ApiNotificationController::class, 'getNotificationListing'])->name('api-business-notifications-listing');
    Route::put('business/notifications/mark-read', [ApiNotificationController::class, 'markNotification'])->name('api-business-notifications-mark-read');
    Route::get('business/notifications/unread-count', [ApiNotificationController::class, 'getUnreadCount'])->name('api-business-notifications-unread-count');

    Route::prefix('influencer')->group(function () {
        Route::get('country-code/list', [ApiRegionController::class, 'getCountryCodeList'])->name('api-get-list-of-country-code');
    });
});



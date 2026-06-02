<?php

use Illuminate\Support\Facades\Route;
use Modules\Platform\Http\Controllers\API\V2\WhatsAppGroupApiController;

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

Route::domain(config('app.api_url'))
    ->prefix('v2')
    ->group(function () {
        Route::middleware('auth:api')->prefix('platforms/whatsapp')->group(function () {
            Route::get('/', [WhatsAppGroupApiController::class, 'index'])->name('api-index-platforms-whatsapp');
            Route::post('/create', [WhatsAppGroupApiController::class, 'store'])->name('api-create-platforms-whatsapp');
            Route::post('{id}/remove', [WhatsAppGroupApiController::class, 'destroy'])->name('api-delete-platforms-whatsapp');
            Route::get('categories', [WhatsAppGroupApiController::class, 'categories'])->name('api-platforms-whatsapp-categories');
        });
    });

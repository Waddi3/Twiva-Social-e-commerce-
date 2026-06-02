<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\API\ApiMobileVersionController;

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
    Route::prefix('version')
//        ->middleware('auth:api')
        ->group(function () {
            Route::get('', [ApiMobileVersionController::class, 'check'])->name('api.version.check');
        });
});

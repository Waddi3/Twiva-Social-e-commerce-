<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\API\V1\ApiBadgeController;

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
    ->prefix('v1/badges')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/', [ApiBadgeController::class, 'list'])->name('api.badges.list');
        Route::post('/', [ApiBadgeController::class, 'update'])->name('api.badges.request.add.new');
    });

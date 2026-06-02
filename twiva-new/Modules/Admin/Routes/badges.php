<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\Admin\Badges\AdminBadgeController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))
    ->middleware('auth:admin')
    ->group(function () {
        Route::get('badges-requests', [AdminBadgeController::class, 'resellerRequested'])->name('admin.badges.index');
        Route::post('badges-requests', [AdminBadgeController::class, 'update'])->name('admin.badges.update');
    });

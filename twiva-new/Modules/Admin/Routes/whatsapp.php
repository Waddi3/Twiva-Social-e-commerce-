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
use Modules\Admin\Http\Controllers\Admin\WhatsAppGroup\AdminWhatsAppGroupController;

Route::domain('admin.' . config('twiva.bases.shop_domain'))->group(function () {
    Route::middleware('auth:admin')->group(function () {
        Route::get('whatsapp-groups', [AdminWhatsAppGroupController::class, 'index'])->name('admin.whatsapp.index');
        Route::get('whatsapp-groups/{id}/show', [AdminWhatsAppGroupController::class, 'show'])->name('admin.whatsapp.show');
        Route::get('whatsapp-groups/verified', [AdminWhatsAppGroupController::class, 'verified'])->name('admin.whatsapp.verified');
    });
});

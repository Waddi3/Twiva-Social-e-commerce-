<?php

use Illuminate\Support\Facades\Route;
use Modules\Categories\Http\Controllers\API\V2\CategoriesController;
use Modules\Categories\Http\Controllers\API\V2\ContentTypeController;

Route::domain(config('app.api_url'))->prefix('v1')->group(function () {

    Route::group(['prefix' => 'content-type'], function () {
        Route::get('/index', [ContentTypeController::class, 'index']);
    });
});

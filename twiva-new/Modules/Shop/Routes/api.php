<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Entities\User;

/*
 *
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::domain(config('app.api_url'))->prefix('v1/user')->middleware('auth:api')->group(function () {
    Route::get('get-products-export-link', function () {
        if (auth('api')->user()->type == User::BusinessType)
        {
            return response()->json([
                'exportShopProductsLink' => \route('business.product.exportCSV', ['business_id' => base64_encode(base64_encode(auth('api')->id()))])
            ]);
        }
        else if (auth('api')->user()->type == User::InfluencerType)
        {
          return  response()->json([
                'exportShopProductsLink' => route('influencer.product.exportCSV', ['influencer_id' => base64_encode(base64_encode(auth('api')->id()))])
            ]);
        }
        abort(403);
    });
});





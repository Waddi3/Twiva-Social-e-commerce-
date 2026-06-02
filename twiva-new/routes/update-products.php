<?php

use Illuminate\Support\Facades\Route;
use Modules\AliExpress\Services\AliExpressOrderService;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Order\Entities\Order;

Route::get('update-influencer-products-price', function () {
    $updated = 0;
    InfluencerShopProducts::with('product')->chunk(100, function ($influencer_products) use (&$updated) {
        // foreach($influencer_products )
        foreach ($influencer_products as $influencer_product) {
            if ($influencer_product->product) {
                $updated += $influencer_product->update(['product_price' => $influencer_product->product->retail_price]);
            }
        }
    });
    dd($updated);
});
// Route::get('check-transaction-update', function(){
//     $transaction = Transaction::find(1353);
//     $response = \Modules\Payment\Services\MPESAService::checkTransactionStatus($transaction);
//     $response_body = json_decode($response->body());
//     dd($response, $response_body);
// });

Route::get('check-place-order', function () {
    $order = Order::find(1678);
    $ali_express_order_service  = new AliExpressOrderService($order);
});

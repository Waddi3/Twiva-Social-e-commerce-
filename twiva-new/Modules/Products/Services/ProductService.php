<?php

namespace Modules\Products\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ProductService
{
    public static function increaseViewProduct($product_id, $user_type, $buyer_id, $session)
    {
        $hours = 6; // 6 hours
        $seconds = 60 * 60 * $hours;
        $key = "key-$product_id-$session" . ($buyer_id ?? '');

        // Retrieve the last viewed time from the cache
        $last_viewed = Cache::get($key);


        // Check if the last viewed time is more than the threshold
        if (!$last_viewed || Carbon::now()->diffInMinutes(Carbon::parse($last_viewed)) > $hours * 60) {
            if ($user_type == User::InfluencerType) {
                $query = InfluencerShopProducts::where('id', $product_id);
            } else {
                $query = Product::where('id', $product_id);
            }

            // Increment the view count
            $query->increment('views');

            // Update the cache with the current time
            Cache::put($key, now(), $seconds);
        }
    }
}

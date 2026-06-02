<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Shops;
use Modules\Platform\Entities\Platform;
use Modules\User\Helper\EarningUser;

class UserInfluenceController
{
    public function dashboard()
    {
        $user = auth()->user();
        $user_id = auth()->id();
        $user->load(['influencerDetails']);
        $userShop = Shops::withCount('influencerProducts')->where('user_id', $user_id)->first();
        if ($userShop) {
            $shopProductsCount = $userShop->influencer_products_count;
            $is_shop_created = 1;
        } else {
            $shopProductsCount = 0;
            $is_shop_created = 0;
        }

        $total_earnings = EarningUser::calculateTotalEarningInfluencer($user, $userShop);
        $earning_this_month = EarningUser::calculateTotalEarningInfluencer($user, $userShop, true);

        $top_five_selling_products = InfluencerShopProducts::with('productDetails:id,rating', 'influencerProductImages')->where('influencer_id', $user->id)->orderBy('total_sale', 'desc')->limit(5)->get();

        $dashboardDetails = [
            'user' => $user,
            'shop_products_count' => $shopProductsCount,
            'is_shop_created' => $is_shop_created,
            'shop_details' => $userShop,
            'eshop_products_export' => base64_encode(base64_encode($user_id)),
            'total_earnings' => $total_earnings,
            'earning_this_month' => $earning_this_month,
            'top_five_selling_products' => $top_five_selling_products,
            'notification_count' => count(auth('api')->user()->unreadnotifications),
            'has_connected' => !array_diff(Platform::AllowedToPostPlatform, $user->UserPlatforms()->whereNotNull('token')->pluck('platform_id')->toArray()) || $user->whatsappGroups()->verified()->count(),
        ];

        return response()->json(['message' => "Dashboard details", 'data' => $dashboardDetails, 'status' => true]);
    }

    //    private function calcualteInfluencerPercentFromOrders($userShop)
    //    {
    //        $total_earnings = 0;
    //        $earning_this_month = 0;
    //        Order::where('shop_id', $userShop?->id)->with('shop:id,user_id,domain', 'orderDetails')
    //            ->where('order_status', '=', Order::DELIVERED_STATUS)
    //            ->chunk(100, function ($orders) use (&$total_earnings, &$earning_this_month) {
    //                foreach ($orders as $order) {
    //                    $InfluencerAmount = $order->InfluencerAmount;
    //                    $total_earnings += $InfluencerAmount;
    //                    if (now()->format('Y-m') == Carbon::parse($order->created_at)->format('Y-m')) {
    //                        $earning_this_month += $InfluencerAmount;
    //                    }
    //                }
    //            });
    //
    //        return ['total_earnings' => $total_earnings, 'earning_this_month' => $earning_this_month];
    //
    //    }


}

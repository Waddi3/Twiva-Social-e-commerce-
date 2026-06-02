<?php

namespace Modules\User\Helper;

use Illuminate\Support\Carbon;
use Modules\Campaign\Entities\PostUser;
use Modules\Influencer\Entities\Shops;
use Modules\Order\Entities\Order;
use Modules\Payment\Entities\WinningPayout;
use Modules\User\Entities\User;

class EarningUser
{
    public static function calculateEarning(User $user, $user_shop = null, $just_this_month = false)
    {
        if (!$user_shop) {
            $user_shop = Shops::withCount('influencerProducts')->where('user_id', $user->id)->first();
        }
        $total_earnings = 0;

        $orders_query = Order::where('shop_id', $user_shop?->id)->with('shop:id,user_id,domain', 'orderDetails')
            ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
            ->where('order_status', '=', Order::DELIVERED_STATUS);

        if ($just_this_month) {
            $orders_query->whereMonth('created_at', date("m"))
                ->whereYear('created_at', date("Y"));
        }

        $orders_query->chunk(100, function ($orders) use (&$total_earnings) {
            foreach ($orders as $order) {
                $total_earnings += $order->InfluencerAmount;
            }
        });

        return $total_earnings;
    }

    public static function calculateBusinessEarning(User $user)
    {

        if ($user->accessing_earnings_prior_to_delivery) {
            $order_statuses = [Order::PROCESSING_STATUS, Order::DISPATCHED_STATUS, Order::DELIVERED_STATUS];
        } else {
            $order_statuses = [Order::DELIVERED_STATUS];
        }

        $total_earnings = 0;
        Order::/*whereHas(['shop.user' => function($query) {
                $query->where('type', User::BusinessType);
            }])
            ->*/where('business_id', $user->id)
            ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
            ->whereIn('order_status', $order_statuses)
            ->with(['orderDetails' => function ($orderDetailsQuery) {
                $orderDetailsQuery->with('userInfo');
            }])->chunk(100, function ($orders) use (&$total_earnings) {
                foreach ($orders as $order) {
                    $total_earnings += $order->businessAmount;
                }
            });
        return $total_earnings;
    }

    public static function calculatePartnerEarning(User $user)
    {
        $orders = Order::where('order_status', Order::DELIVERED_STATUS);
        // Get last payout
        $lastPayout = WinningPayout::where('user_id', $user->id)
            ->select('created_at')
            ->where('status', WinningPayout::STATUS_SUCCESS)
            ->orderByDesc('id')
            ->first();
        if ($lastPayout) {
            $orders->where('created_at', '>', $lastPayout->created_at);
        }
        return $orders->sum('shipping_amount');
    }

    public static function calculateCampaignEarning($user, $just_this_month = false)
    {
        $sum_campaign_earnings = 0;
        $post_users_query = PostUser::with(['post.prices' => function ($with_post_price) use ($user) {
            $with_post_price->where('status', PostUser::HiredStatus)
                ->relatedToUser($user->id);
        }])
            ->where('status', PostUser::HiredStatus)
            // ->whereDoesntHave('post.transactions')
            ->where('is_payable', 1)
            ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
            ->relatedToUser($user->id);

        if ($just_this_month) {
            $post_users_query->whereMonth('created_at', date("m"))
                ->whereYear('created_at', date("Y"));
        }

        $post_users_query->chunk(100, function ($post_users) use (&$sum_campaign_earnings) {
            foreach ($post_users as $post_user) {
                // if should to be calculate 85% replace ->final_price with ->influencerAmount
                $sum_campaign_earnings += $post_user?->post?->prices?->last()?->influencerAmount;
            }
        });

        return $sum_campaign_earnings;
    }

    public static function calculateEarningNotPaid($user, $orders_earnings, $campaign_earning)
    {
        $winnings_payout_paid = WinningPayout::where('user_id', $user->id)->where('status', WinningPayout::STATUS_SUCCESS)->sum('transaction_amount');
        $earning_not_paid = ($orders_earnings + $campaign_earning) - $winnings_payout_paid;
        return [$earning_not_paid, $winnings_payout_paid];
    }

    public static function calculateTotalEarningInfluencer($user, $user_shop, $just_this_month = false)
    {
        if (!$user_shop) {
            $user_shop = Shops::withCount('influencerProducts')->where('user_id', $user->id)->first();
        }

        $total_earnings = self::calculateEarning($user, $user_shop, $just_this_month);

        $sum_campaign_earnings = self::calculateCampaignEarning($user, $just_this_month);

        return $total_earnings + $sum_campaign_earnings;
    }



    /**
     * @deprecate
     */
    public static function calculateTotalEarningBusiness(User $user)
    {
        $total_earnings = 0;
        Order::where('business_id', $user->id)
            // ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
            ->where('order_status', Order::DELIVERED_STATUS)
            ->with(['orderDetails' => function ($orderDetailsQuery) {
                $orderDetailsQuery->with('userInfo');
            }])->chunk(100, function ($orders) use (&$total_earnings/*, &$earning_not_paid*/) {
                foreach ($orders as $order) {
                    $total_earnings += $order->businessAmount;
                }
            });

        return $total_earnings;
    }
}

<?php

namespace Modules\Payment\Http\Controllers\API\V3\Influencer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Campaign\Entities\PostPrice;
use Modules\Campaign\Entities\PostUser;
use Modules\Influencer\Entities\Shops;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Payment\Http\Resources\Influencer\Earnings\CampaignEarningsCollection;
use Modules\Payment\Http\Resources\Influencer\Earnings\ShopEarningsCollection;
use Modules\User\Helper\EarningUser;

class EarningsHistoryController extends Controller
{
    public function indexCampaignEarnings()
    {
        // Carbon::now()->subHours(24)
        $postsIds = PostUser::relatedToUser()
            // ->where( 'is_payable', 1)
            ->where('status', PostUser::HiredStatus)
            ->pluck('post_id')
            ->toArray();

        $campaignEarnings = PostPrice::relatedToUser()
            ->whereIn('post_id', $postsIds)
            ->get();

        $sumCampaignEarnings = 0;
        foreach ($campaignEarnings as $campaignEarning) {
            $sumCampaignEarnings += $campaignEarning->influencerAmount;
        }

        $shopEarnings = OrderDetails::with(['Order'])
            ->whereHas('productDetails')
            ->whereHas('Order', function ($orderQuery) {
                $orderQuery->where('shop_id', auth('api')->user()->shop->id)
                    ->where('order_status', '=', Order::DELIVERED_STATUS);
            })
            ->with('productDetails:id,product_title,product_price,product_id', 'productImages')
            ->orderBy('updated_at', 'DESC')->get();


        // list($total_earnings, $earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarning(auth('api')->user(), auth('api')->user()->shop);

        $user = auth('api')->user();
        $user_id = auth()->id();
        $userShop = Shops::withCount('influencerProducts')->where('user_id', $user_id)->first();

        $orders_earnings = EarningUser::calculateEarning($user, $userShop);
        $campaign_earning = EarningUser::calculateCampaignEarning($user);

        list($earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $orders_earnings, $campaign_earning);

        return response()->json(
            [
                'data' => CampaignEarningsCollection::collection($campaignEarnings)->collection,
                //                'shop_earnings' => $this->getTotalShopEarnings($shopEarnings),
                'total_earnings' => $orders_earnings + $campaign_earning,
                'shop_earnings' => $earning_not_paid,
                'winnings_payout_paid' => $winnings_payout_paid,
                'campaign_earnings' => $campaign_earning, // $this->getTotalCampaignEarnings($campaignEarnings),
            ]
        );
    }

    public function indexOrderEarnings(Request $request)
    {
        $shopEarnings = OrderDetails::with(['Order'])
            ->whereHas('productDetails')
            ->whereHas('Order', function ($orderQuery) {
                $orderQuery->where('shop_id', auth('api')->user()->shop->id)
                    ->where('order_status', '=', Order::DELIVERED_STATUS);
            })
            ->with(['productDetails:id,product_title,product_price,product_id', 'productImages'])
            ->orderBy('updated_at', 'DESC');

        $totalEarningsCollection = clone $shopEarnings->get();
        $shopEarnings = $shopEarnings->paginate(10);

        // list($total_earnings, $earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarning(auth('api')->user(), auth('api')->user()->shop);

        $user = auth('api')->user();
        $user_id = auth()->id();
        $userShop = Shops::withCount('influencerProducts')->where('user_id', $user_id)->first();

        $orders_earnings = EarningUser::calculateEarning($user, $userShop);
        $campaign_earning = EarningUser::calculateCampaignEarning($user);

        list($earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $orders_earnings, $campaign_earning);

        $user = auth('api')->user();
        $user_id = auth()->id();
        $userShop = Shops::withCount('influencerProducts')->where('user_id', $user_id)->first();

        $orders_earnings = EarningUser::calculateEarning($user, $userShop);
        $campaign_earning = EarningUser::calculateCampaignEarning($user);

        list($earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $orders_earnings, $campaign_earning);

        // TODO::H check $earning_not_paid show in winnings payout paid
        return response()->json([
            'status' => true,
            'data' => ShopEarningsCollection::collection($shopEarnings)->resource,
            //            'shop_earnings' => $this->getTotalShopEarnings($totalEarningsCollection),
            'total_earnings' => $orders_earnings + $campaign_earning,
            'shop_earnings' => $earning_not_paid,
            'winnings_payout_paid' => $winnings_payout_paid,
            'campaign_earnings' => $campaign_earning, // $this->getTotalCampaignEarnings(),
        ]);
    }

    private function getTotalCampaignEarnings($campaignEarnings = null): int
    {
        if (!$campaignEarnings) {
            $campaignEarnings = PostPrice::where('infulancer_id', auth('api')->id())
                ->where('status', PostUser::HiredStatus)
                ->get();
        }

        $sumCampaignEarnings = 0;
        foreach ($campaignEarnings as $campaignEarning) {
            $sumCampaignEarnings += $campaignEarning->influencerAmount;
        }
        return $sumCampaignEarnings;
    }

    private function getTotalShopEarnings($totalEarningsCollection): int
    {
        $totalShopCollection = 0;
        foreach ($totalEarningsCollection as $orderEarnings) {
            $totalShopCollection += $orderEarnings->influencerAmount;
        }
        return $totalShopCollection;
    }
}

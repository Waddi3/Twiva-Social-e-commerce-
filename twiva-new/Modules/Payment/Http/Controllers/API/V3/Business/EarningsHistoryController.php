<?php

namespace Modules\Payment\Http\Controllers\API\V3\Business;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Payment\Http\Resources\Business\EarningsHistoryCollection;
use Modules\User\Helper\EarningUser;

class EarningsHistoryController extends Controller
{
    public function index(Request $request)
    {

        $user = auth('api')->user();
        $order_amount_form_buyer = EarningUser::calculateBusinessEarning($user);

        if($user->accessing_earnings_prior_to_delivery){
            $order_statuses = [Order::PROCESSING_STATUS, Order::DISPATCHED_STATUS, Order::DELIVERED_STATUS];
        }else{
            $order_statuses = [Order::DELIVERED_STATUS];
        }

        $orderList = Order::where('business_id', auth('api')->id())
        ->whereIn('order_status', $order_statuses)
        ->with(['orderDetails' => function ($orderDetailsQuery) {
            $orderDetailsQuery->with('userInfo');
        }]);
        if (Str::length(request('search')))
        {
            $orderList->where('id', 'like', '%' . request('search') . '%')
                ->orWhere(function ($query) {
                    $query->whereHas('orderDetails.userInfo', function ($query) {
                        $query->where('email', 'like', '%' . request('search') . '%');
                    });
                });
        }

        $orderList = $orderList->orderBy('id', 'desc')->paginate(10);


        list($earning_not_paid, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $order_amount_form_buyer, 0);

        return response()->json(['success' => true,
            'message' => "Order Listing",
            'data' => EarningsHistoryCollection::collection($orderList),
            'current_page' => $orderList->currentPage(),
            'last_page' => $orderList->lastPage(),
            'total_results' => $orderList->total(),
            'total_earnings' => $earning_not_paid
        ]);
    }
}

<?php

namespace Modules\Partner\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Order\Entities\Order;
use Modules\Partner\Enums\ProductShipping;
use Modules\Partner\Http\Resources\PartnerEarningResource;
use Modules\Payment\Entities\WinningPayout;

class PartnerEarningsController extends Controller
{
    public function history()
    {
        // Order List
        $orders = Order::where('order_status', Order::DELIVERED_STATUS)
            ->with(['orderDetails' => function ($orderDetailsQuery) {
                $orderDetailsQuery->select('id', 'order_id', 'quantity', 'amount', 'status', 'created_at')->with('userInfo');
            }])
            ->where('partner_id', auth::id())
            /*->whereHas('orderDetails.productDetails.productDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })*/;

        // Total Earning
        $total_earning = Order::where('order_status', Order::DELIVERED_STATUS)
            /*->whereHas('orderDetails.productDetails.productDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })*/
            ->where('partner_id', auth::id())
            ->sum('shipping_amount');

        // Total Earnt this month
        $total_earnt_this_month = Order::where('order_status', Order::DELIVERED_STATUS)
            ->where('partner_id', auth::id())
            /*->whereHas('orderDetails.productDetails.productDetails', function ($query) {
                $query->where('shipping_by', ProductShipping::Partner->value);
            })*/
            ->whereBetween('created_at',
                [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
            ->sum('shipping_amount');

        // Paid earnings
        $winnings_payout_paid = 0;
        $user = Auth::user();
        if( $user ) {
            $winnings_payout_paid = WinningPayout::where('user_id', $user->id)->where('status', WinningPayout::STATUS_SUCCESS)->sum('transaction_amount');
        }

        return response()->json([
            'success' => true,
            'message' => "Order Listing",
            'data' => PartnerEarningResource::collection($orders->orderBy('id', 'desc')->paginate(10))->resource,
            /*'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total_results' => $orders->total(),*/
            'total_earnings' => $total_earning,
            'total_earnt_this_month' => $total_earnt_this_month,
            'unpaid_earnings' => $total_earning - $winnings_payout_paid
        ]);

        //return response()->json(['data' => PartnerOrdersResource::collection($orders->paginate(10))->resource]);
    }
}

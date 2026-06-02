<?php

namespace Modules\Business\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Platform\Entities\Platform;
use Modules\User\Entities\User;

class ApiBusinessDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth('api')->user();
        if ($user) {

            $user->load(['businessDetails']);
            $userProduct = User::withCount('businessProducts')->where('id', $user->id)->first();
            if ($userProduct) {
                $productsCount = $userProduct->business_products_count;
            } else {
                $productsCount = 0;
            }
            $start = (new Carbon('first day of this month'))->startOfDay();
            $end = (new Carbon('last day of this month'))->endOfDay();

            if($user->accessing_earnings_prior_to_delivery){
                $order_statuses = [Order::PROCESSING_STATUS, Order::DISPATCHED_STATUS, Order::DELIVERED_STATUS];
            }else{
                $order_statuses = [Order::DELIVERED_STATUS];
            }

            $this_month_orders = Order::where('business_id', auth('api')->id())->with('shop:id,user_id,domain', 'orderDetails')
                ->whereIn('order_status', $order_statuses)
                //                ->whereMonth('created_at', now()->month)
                //                ->whereBetween('orders.delivery_date', [$start, $end])
                ->whereDate('created_at', 'like', now()->format('Y-m') . '%')
                ->get();
            $earning_this_month = 0;
            $sold_product_this_month = 0;

            foreach ($this_month_orders as $item) {
                $earning_this_month += $item->businessAmount;
                $sold_product_this_month += $item->qty;
            }

            $total_earnings_orders = Order::where('business_id', auth('api')->id())->with('shop:id,user_id,domain', 'orderDetails')
                ->whereIn('order_status', $order_statuses)
                ->get();
            $total_earnings = 0;
            foreach ($total_earnings_orders as $item) {
                $total_earnings += $item->businessAmount;
            }

            //
            $recent_orders = OrderDetails::with(['Order'])
                ->whereHas('Order', function ($orderQuery) use ($user) {
                    $orderQuery->where('business_id', $user->id)
                        ->where('order_status', Order::PLACED_STATUS);
                })
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
            foreach ($recent_orders as $order) {
                if ($order?->Order?->shop->user?->type == User::InfluencerType) {
                    $actual_product = Product::with('productImages')->where('id', $order->productDetails->product_id)->first();
                    $order->business_product_detail = $actual_product;
                    $order->total_sale = $actual_product?->total_sale;
                }
            }

            $top_five_selling_products = Product::with('productImages')->where('user_id', $user->id)->where('total_sale', '>', 0)->orderBy('total_sale', 'desc')->limit(5)->get();

            $dashboardDetails = [
                'user' => $user,
                'products_count' => $productsCount,
                'total_earnings' => $total_earnings,
                'earning_this_month' => $earning_this_month,
                'sold_product_this_month' => (int)$sold_product_this_month,
                'recent_orders' => $recent_orders ?? [],
                'top_five_selling_products' => $top_five_selling_products,
                'notification_count' => count(auth('api')->user()->unreadnotifications),
                'influencer_count' => $user->influencerCount,
                'pay_on_delivery' => $user->businessDetails->pay_on_delivery,
                'has_connected' => !array_diff(Platform::AllowedToPostPlatform, $user->UserPlatforms()->whereNotNull('token')->pluck('platform_id')->toArray()) || $user->whatsappGroups()->verified()->count(),
            ];

            $success_message = array('message' => "Dashboard details", 'data' => $dashboardDetails, 'status' => true);
            return response()->json($success_message, 200);
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }
    }
}

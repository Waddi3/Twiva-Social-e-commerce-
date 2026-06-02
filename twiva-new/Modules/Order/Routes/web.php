<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Order\Entities\Order;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\User\Entities\User;
use Modules\User\Entities\UserReferralCode;

Route::get('/full-margin', function () {
    $user = User::find(14264);
    $order = $user->shop->orders()->orderBy('id', 'DESC')->first();

    if ($user->accessing_earnings_prior_to_delivery) {
        $order_statuses = [Order::PROCESSING_STATUS, Order::DISPATCHED_STATUS, Order::DELIVERED_STATUS];
    } else {
        $order_statuses = [Order::DELIVERED_STATUS];
    }

    $total_earnings = 0;

    $orders = Order::where('business_id', $user->id)
        ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
        ->whereIn('order_status', $order_statuses)
        ->with(['orderDetails' => function ($orderDetailsQuery) {
            $orderDetailsQuery->with('userInfo');
        }])->get();

    //dd($orders[1]->businessAmount);
    //dd($orders[1]->id);


    Order::/*whereHas(['shop.user' => function($query) {
                $query->where('type', User::BusinessType);
            }])
            ->*/ where('business_id', $user->id)
        ->whereDate('created_at', '>=', config('twiva.bases.date_of_start_new_system') . '%')
        ->whereIn('order_status', $order_statuses)
        ->with(['orderDetails' => function ($orderDetailsQuery) {
            $orderDetailsQuery->with('userInfo');
        }])->chunk(100, function ($orders) use (&$total_earnings) {
            foreach ($orders as $order) {
                $total_earnings += $order->businessAmount;
            }
        });

    dd($total_earnings);
    //return $total_earnings;
});

Route::get('event-listening-points', function () {

    $order = Order::where('id', 488)->first();

    $business = $order->business;
    $shop_user = $order->shop->influencer;

    $order_amounts = Order::where('business_id', $business->id)->where('order_status', Order::DELIVERED_STATUS)->sum('order_amount');


    $invitor = UserReferralCode::with('invitor')
        ->where('invited_id', $business->id)
        ->whereNull('checked_at')
        ->first();

    if ($order_amounts > 50000) {
        $invitor->invitor->update([
            'points' => $invitor->invitor->points + 4,
            'checked_at' => now(),
        ]);
    }

    $order_amounts = Order::where('business_id', $business->id)->where('order_status', Order::DELIVERED_STATUS)->sum('order_amount');

    $invitor = UserReferralCode::with('invitor')
        ->where('invited_id', $shop_user->id)
        ->whereNull('checked_at')
        ->first();

    if ($invitor and $invitor->invited_type == User::BusinessType) {
        $invitor->invitor->update([
            'points' => $invitor->invitor->points + 4,
            'checked_at' => now(),
        ]);
    } elseif ($invitor and $invitor->invited_type == User::InfluencerType) {
        $invitor->invitor->update([
            'points' => $invitor->invitor->points + 1,
            'checked_at' => now(),
        ]);
    }
    dd(
        [
            $business->id => $business->type,
            $shop_user->id => $shop_user->type,
        ]
        , $invitors);

//    return event(new  DeliveredOrderEvent($order));

});

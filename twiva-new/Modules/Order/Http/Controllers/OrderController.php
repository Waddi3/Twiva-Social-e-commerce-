<?php


namespace Modules\Order\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;

class OrderController
{
    public function index($account)
    {
        $orders = Order::where('buyer_id', Auth::guard('buyer')->id())->orderByDesc('id')->get();
        return view('order::buyer.index', ['orders' => $orders]);
    }

    public function show($account, Order $order)
    {
        return response()->json(['view' => view('order::buyer._details', ['order' => $order])->render()]);
    }
}

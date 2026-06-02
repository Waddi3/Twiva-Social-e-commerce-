<?php

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Entities\Order;
use Modules\Order\Events\OrderPaidSuccessfully;

class EmailSeller implements ShouldQueue
{
    public $tries = 3;

    public function __construct()
    {
        //
    }

    public function handle(OrderPaidSuccessfully $event)
    {
        $order = Order::select('id')->where('id', $event->order)
            ->with([
                    'orderList' => function ($q) {
                        return $q->select('id', 'business_id', 'order_id')
                            ->with(['businessInfo' =>
                                function ($q) {
                                    return $q->select('id', 'name', 'email');
                                }]);
                    }]
            )->first();

        Mail::send('emails.Business.newOrder',
            [
                'id' => $order->id,
                'date' => $order->created_at->format('d-M-Y'),
                'buyer' => ($order->orderList[0]->businessInfo->name ?? $order->orderList[0]->businessInfo->email),
            ],
            function ($m) use ($order) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($order->orderList[0]->businessInfo->email)->subject('New Order Recieved');
            });
    }
}

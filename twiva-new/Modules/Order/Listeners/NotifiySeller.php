<?php

namespace Modules\Order\Listeners;

use Modules\Notification\Entities\Notification;
use Modules\Order\Events\OrderPaidSuccessfully;

class NotifiySeller
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(OrderPaidSuccessfully $event)
    {
        $order = Order::select('id')->where('id', $event->order)
            ->with([
                    'orderList' => function ($q) {
                        return $q->select('id', 'business_id','order_id')
                            ->with(['businessInfo' =>
                                function ($q) {
                                    return $q->select('id');
                                }]);
                    }]
            )->first();

        $notification['user_id'] = $order->orderList[0]->businessInfo->id;
        $notification['request_type'] = 1;
        $notification['request_id'] = $order->id;
        $notification['description'] = 'You recieved a new order';
        Notification::create($notification);
    }
}

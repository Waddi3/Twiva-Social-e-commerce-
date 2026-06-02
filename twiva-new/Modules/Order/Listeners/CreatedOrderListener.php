<?php

namespace Modules\Order\Listeners;

use Modules\Order\Events\CreatedOrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\AliExpress\Entities\AliExpressOrder;
use Modules\AliExpress\Services\AliExpressOrderService;
use Modules\AliExpress\Services\AliExpressService;
use Modules\Order\Entities\Order;

class CreatedOrderListener implements ShouldQueue
{
    use InteractsWithQueue;


    // public $queue = 'default';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(public AliExpressService $ali_express_service) {}

    /**
     * Handle the event.
     *
     * @param  CreatedOrderEvent  $event
     * @return void
     */
    public function handle(CreatedOrderEvent $event)
    {
        $order = $event->order;
        $ali_express_account = getAliExpressAccount();
        $order_created_in_ali_express = AliExpressOrder::query()
            ->where('order_id', $order->id)
            ->whereNotNull('order_list')
            ->exists();
        if (!$order_created_in_ali_express) { // check if this order created in ali express already or not
            if ( // check if the order has been paid
                (is_null($order->latestTransaction->first()?->status) && $order->payment_status == Order::PAID)
                || ($order->latestTransaction->first()?->status && $order->payment_status == Order::PAID)
                || ($order->payment_status == Order::PAY_ON_DELIVERY)
            ) {
                if ($order->business_id == $ali_express_account->id) { // check if the order is related to the ali express account
                    // call api placed order in ali express service
                    // to create this order in ali express site
                    $ali_express_order_service  = new AliExpressOrderService($order);
                }
            }
        }
    }
}

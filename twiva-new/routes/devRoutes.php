<?php

use Modules\Buyer\Notifications\EventProductTicketDispatchedNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Mail\EventProductPaymentMail;

Route::get('test-ticket-mail', function ()
{
    $order = Order::withWhereHas('orderList', function ($orderListQuery)
    {
        $orderListQuery->withWhereHas('businessProductDetails', function ($businessProductDetailsQuery)
        {
            $businessProductDetailsQuery->withWhereHas('eventTicketProduct');
        });
    })->first();
    foreach ($order->orderList as $orderList)
    {
        $order->shippingAddress->notify(new EventProductTicketDispatchedNotification(
            ticketProduct: $orderList->businessProductDetails,
            delivery_code: $order->fresh()->delivery_code
        ));
    }

    return "Email has been sent!";
});
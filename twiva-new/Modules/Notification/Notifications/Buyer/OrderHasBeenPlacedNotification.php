<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\System\Entities\System;
use Modules\Order\Entities\Order;

class OrderHasBeenPlacedNotification extends BuyerBaseNotificationClass
{
    public
    array $details;
    private $orders;


    public function __construct($ordersIDS)
    {
        $this->onQueue('order-actions');
        $this->orders = Order::whereIn('id', $ordersIDS)->get();
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_EMAIL;

        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $totalAmount = 0;
        foreach ($this->orders as $order) {
            $totalAmount += ($order->order_amount + $order->shipping_amount);
        }
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'order_has_been_placed_notification_title')?->title; // 'Your orders has been placed.';
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your orders has been placed, you have paid: $totalAmount ksh";
        $details['email_message'] = "Your orders  has been successfully placed.
         You will receive an email notification once the order is processed";
        $details['name'] = $this->orders[0]->shippingAddress->contactName;
        $details['email'] = $this->orders[0]->shippingAddress->email;
        $details['email_type'] = "order_details";
        $details['orders'] = $this->orders;


        return $details;
    }
}

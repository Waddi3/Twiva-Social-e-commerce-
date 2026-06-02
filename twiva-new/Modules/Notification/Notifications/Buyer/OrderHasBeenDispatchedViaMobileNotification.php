<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\System\Entities\System;

class OrderHasBeenDispatchedViaMobileNotification extends BuyerBaseNotificationClass
{
    public
    array $details;
    private $order;


    public function __construct($order)
    {
        $this->order = $order;
        $this->onQueue('order-actions');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {

        $via = NotificationEntity::VIA_MOBILE;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $insert_delivery_code_url = route('buyer.delivery.code.insert');
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'order_has_been_dispatched_notification_title')?->title; // 'Your order has been dispatched.';
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your order with ID: {$this->order->id} has been dispatched. Your delivery is on its way! Remember to give the code {$this->order->delivery_code} to our delivery personnel to confirm delivery.";
        $details['email_message'] = "Your order with ID: {$this->order->id} is on its way! Your delivery is on its way! Remember to give the code {$this->order->delivery_code} to our delivery personnel to confirm delivery.";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = $this->order->id;

        $details['name'] = $this->order->shippingAddress->contactName;
        $details['email'] = $this->order->shippingAddress->email;
        return $details;
    }
}

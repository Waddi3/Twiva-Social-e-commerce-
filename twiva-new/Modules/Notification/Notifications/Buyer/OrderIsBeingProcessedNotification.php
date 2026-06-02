<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\System\Entities\System;

class OrderIsBeingProcessedNotification extends BuyerBaseNotificationClass
{
    public
    array $details;
    private $order;


    public function __construct($order)
    {
        $this->onQueue('order-actions');
        $this->order = $order;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_EMAIL;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'order_is_being_processed_notification_title')?->title; // "Your order is being processed.";
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your order with ID: {$this->order->id} being processed. {$this->order->tentative_period_text()}";
        $details['email_message'] = "Your order with ID: {$this->order->id} has been received and is being processed. {$this->order->tentative_period_text()}";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = $this->order->id;
        $details['name'] = $this->order->shippingAddress->contactName;
        $details['email'] = $this->order->shippingAddress->email;


        return $details;
    }
}

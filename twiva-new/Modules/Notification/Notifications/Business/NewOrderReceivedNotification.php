<?php

namespace Modules\Notification\Notifications\Business;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\System\Entities\System;

class NewOrderReceivedNotification extends BaseNotificationClass
{
    public array $details;
    private $order;


    public function __construct($order)
    {
        $this->order = $order;
        $this->onQueue('order-actions');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);
    }


    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'new_order_received_notification_title')?->title; // 'You have received a new order.';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'new_order_received_notification_fcm_title')?->title; // 'new order received';
        $details['sms_message'] = 'You have received a new order.';
        $details['email_message'] = "You have received a new order. See all customer details here.";
        $details['flutter_action'] = NotificationEntity::VISIT_ORDER_ACTION;
        $details['reason_id'] = $this->order->id;

        return $details;
    }
}

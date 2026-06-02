<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;

class OrderHasBeenCanceledNotification extends BuyerBaseNotificationClass
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
        $via = NotificationEntity::VIA_EMAIL_MOBILE;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'order_has_been_canceled_notification_title')?->title; // 'Your order has been cancelled by the seller.';
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your order with ID: {$this->order->id} has been cancelled by the seller.";
        $details['email_message'] = "Your order has been cancelled by the seller.";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = $this->order->id;
        $details['name'] = $this->order->shippingAddress->contactName;
        $details['email'] = $this->order->shippingAddress->email;
        return $details;
    }
}

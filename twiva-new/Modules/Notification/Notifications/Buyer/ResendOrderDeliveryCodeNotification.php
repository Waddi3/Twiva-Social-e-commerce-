<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;

class ResendOrderDeliveryCodeNotification extends BuyerBaseNotificationClass
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
        $details['title'] = 'You order delivery code';
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your order delivery code is: {$this->order->delivery_code}";
        $details['email_message'] = "Your order delivery code is: {$this->order->delivery_code}";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = $this->order->id;

        $details['name'] = $this->order->shippingAddress->contactName;
        $details['email'] = $this->order->shippingAddress->email;
        return $details;
    }
}

<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\System\Entities\System;

class PurchaseHasBeenMadeFromEshopNotification extends BaseNotificationClass
{
    public array $details;


    public function __construct()
    {
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
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'purchase_has_been_made_from_eshop_notification_title')?->title; // 'A Purchase has been made from your e-shop, You will receive your commission once the product is successfully delivered';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'purchase_has_been_made_from_eshop_notification_fcm_title')?->title; // 'New order';
        $details['sms_message'] = 'A Purchase has been made from your e-shop, You will receive your commission once the product is successfully delivered';
        $details['email_message'] = "A Purchase has been made from your e-shop, You will receive your commission once the product is successfully delivered";
        $details['flutter_action'] = NotificationEntity::VISIT_MY_EARNINGS_ACTION;
        $details['reason_id'] = null;

        return $details;
    }
}

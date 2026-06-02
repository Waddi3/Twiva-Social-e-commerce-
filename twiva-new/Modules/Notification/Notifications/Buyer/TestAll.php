<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Notifications\BaseNotificationClass;

class TestAll extends BuyerBaseNotificationClass
{
    public array $details;

    public function __construct()
    {
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = 'FCM IS OK';
        $details['fcm_title'] = 'Iam A Short Title';
        $details['sms_message'] = 'Iam A Short Title';
        $details['email_message'] = 'Iam A Short Title';
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = "12";
        $details['email'] = "az@az.az";
        $details['name'] = "az@az.az";

        return $details;
    }
}

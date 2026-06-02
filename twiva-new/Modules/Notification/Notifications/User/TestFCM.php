<?php

namespace Modules\Notification\Notifications\User;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Notifications\BaseNotificationClass;

class TestFCM extends BaseNotificationClass
{
    public array  $details;

    public function __construct()
    {
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_FCM;
    }

    function makeDetails(): array
    {
        $details['title'] = 'FCM IS OK';
        $details['fcm_title'] = 'Iam A Short Title';
        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = "12";

        return $details;
    }
}

<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class WelcomeToTwivaNotification extends BaseNotificationClass
{
    public array $details;


    public function __construct()
    {
        $this->onQueue('system');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_EMAIL;
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'welcome_to_twiva_notification_title')?->title; // 'Welcome To Twiva!';
        $details['fcm_title'] = null;
        $details['sms_message'] = null;
        $details['email_message'] = "Welcome to Twiva! Start earning by clicking here.";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = null;

        return $details;
    }
}

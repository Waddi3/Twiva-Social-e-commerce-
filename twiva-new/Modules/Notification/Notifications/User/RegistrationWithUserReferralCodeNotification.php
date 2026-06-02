<?php

namespace Modules\Notification\Notifications\User;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class RegistrationWithUserReferralCodeNotification extends BaseNotificationClass
{
    public array $details;


    public function __construct()
    {
        $this->onQueue('system');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_FCM;
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'registration_with_user_referral_code_notification_title')?->title; // "A user has registered with your referral code.";
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'registration_with_user_referral_code_notification_fcm_title')?->title; //  "referral code registration";
        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::VISIT_REFERRAL_POINTS_ACTION;
        $details['reason_id'] = null;
        return $details;
    }
}

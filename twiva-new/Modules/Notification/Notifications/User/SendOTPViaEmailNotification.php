<?php

namespace Modules\Notification\Notifications\User;

use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class SendOTPViaEmailNotification extends BaseNotificationClass
{
    public array $details;
    private $otpCode;


    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
        $this->onQueue('send-otp');

        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'send_otp_via_email_notification_title')?->title; // 'OTP code request';
        $details['fcm_title'] = null;
        $details['sms_message'] = null;
        $details['email_message'] = "Twiva Otp Code is :{$this->otpCode}";
        $details['flutter_action'] = null;
        $details['reason_id'] = null;
        return $details;
    }
}

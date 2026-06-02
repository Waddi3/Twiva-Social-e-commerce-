<?php

namespace Modules\Notification\Notifications\User;

use Apxcde\OnfonSms\OnfonChannel;
use Modules\Notification\Notifications\BaseNotificationClass;
use NotificationChannels\Twilio\TwilioChannel;

class SendOTPViaMobileNotification extends BaseNotificationClass
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
        return [OnfonChannel::class];
    }

    function makeDetails(): array
    {
        $details['title'] = null;
        $details['fcm_title'] = null;
        $details['sms_message'] = "{$this->otpCode} is your Twiva verification code.";
        $details['email_message'] = null;
        $details['flutter_action'] = null;
        $details['reason_id'] = null;
        return $details;
    }
}

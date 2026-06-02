<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;

class ResetPasswordViaSmsNotification extends BuyerBaseNotificationClass
{

    public $sms_url;


    public function __construct($sms_url)
    {
        $this->sms_url = $sms_url;
        $this->onQueue('system');

        return parent::__construct($this->makeDetails());
    }

    function via($notifiable)
    {
        $via = NotificationEntity::VIA_MOBILE;
        return self::checkSystem($via);
    }

    function makeDetails()
    {
        return [
            'sms_message' => "You recently requested to reset the password for your Twiva account. Click the link below to proceed. {$this->sms_url} This password reset link is only valid for the next 30 minutes.",
        ];
    }
}

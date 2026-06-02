<?php

namespace Modules\Notification\Notifications\Buyer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;

class VerifyPhoneNumberNotification extends BuyerBaseNotificationClass
{
    public
    array $details;
    private $buyer;


    public function __construct($buyer)
    {
        $this->onQueue('system');
        $this->buyer = $buyer;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_MOBILE;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = "Verify phone number";
        $details['fcm_title'] = null;
        $details['sms_message'] = "Your OTP code is: {$this->buyer->OTP()->first()->code}";
        $details['email_message'] = "Your OTP code is: {$this->buyer->OTP()->first()->code}";
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = null;
        return $details;
    }
}

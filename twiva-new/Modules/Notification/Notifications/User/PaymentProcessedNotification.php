<?php

namespace Modules\Notification\Notifications\User;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\System\Entities\System;

class PaymentProcessedNotification extends BaseNotificationClass
{
    public array $details;


    public function __construct()
    {
        $this->onQueue('system');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);

    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'payment_processed_notification_title')?->title; // "Twiva has processed your payment!";
        $details['fcm_title'] = null;
        $details['sms_message'] = "Twiva has processed your payment!";
        $details['email_message'] = "Twiva has processed your payment. You can now proceed to withdraw your funds.";
        $details['flutter_action'] = NotificationEntity::VISIT_PAYMENT_HISTORY_ACTION;
        $details['reason_id'] = null;
        return $details;
    }
}

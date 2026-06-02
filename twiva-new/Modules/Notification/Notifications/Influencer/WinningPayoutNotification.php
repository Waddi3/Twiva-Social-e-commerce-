<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\Payment\Entities\WinningPayout;
use Modules\System\Entities\System;

class WinningPayoutNotification extends BaseNotificationClass
{
    public array $details;
    public $winning_payout;


    public function __construct($winning_payout)
    {
        $this->onQueue('system');
        $this->winning_payout = $winning_payout;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);

    }

    function makeDetails(): array
    {
        $details['title'] = $this->title();
        $details['fcm_title'] = $this->fcmTitle();
        $details['sms_message'] = $this->smsMessage();
        $details['email_message'] = $this->emailMessage();
        $details['flutter_action'] = NotificationEntity::NOTHING_ACTION;
        $details['reason_id'] = null;

        return $details;
    }

    private function title()
    {
        // Your Profit Payment Successfully Processed - Twiva
        // Apology for Unsuccessful Payment - Please Resubmit Your Request - Twiva
        return NotificationTitle::select('title')->firstWhere('slug', "winning_payout_notification_{$this->winning_payout->status}_title")?->title;
    }

    private function fcmTitle()
    {
        return NotificationTitle::select('title')->firstWhere('slug', "winning_payout_notification_{$this->winning_payout->status}_fcm_title")?->title;
    }

    private function smsMessage()
    {
        return $this->winning_payout->status == WinningPayout::STATUS_SUCCESS
            ? "Your profit payment has been successfully processed. We hope this experience has been enjoyable for you, and we are always at your service. Twiva"
            : "We apologize for the inconvenience, but it seems that the profit payment was unsuccessful. We sincerely regret any inconvenience this may have caused. Kindly consider submitting a new request, and we appreciate your understanding. We are always here to assist you. Twiva";
    }

    private function emailMessage()
    {
        return $this->winning_payout->status == WinningPayout::STATUS_SUCCESS
            ? "Your profit payment has been successfully processed. We hope this experience has been enjoyable for you, and we are always at your service. Twiva"
            : "We apologize for the inconvenience, but it seems that the profit payment was unsuccessful. We sincerely regret any inconvenience this may have caused. Kindly consider submitting a new request, and we appreciate your understanding. We are always here to assist you. Twiva";
    }
}

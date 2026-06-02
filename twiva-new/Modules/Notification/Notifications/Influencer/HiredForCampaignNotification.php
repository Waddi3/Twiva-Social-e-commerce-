<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\System\Entities\System;

class HiredForCampaignNotification extends BaseNotificationClass
{
    public
    array $details;
    private $campaign;


    public function __construct($campaign)
    {
        $this->onQueue('campaign-actions');
        $this->campaign = $campaign;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'hired_for_campaign_notification_title')?->title; // 'Congratulations! You have been hired for a campaign you applied for';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'hired_for_campaign_notification_fcm_title')?->title; // 'Congratulations!';
        $details['sms_message'] = 'Congratulations! You have been hired for a campaign you applied for';
        $details['email_message'] = "Congratulations! You have been hired for a campaign you applied for";
        $details['flutter_action'] = NotificationEntity::VISIT_INVITATION_ACTION;
        $details['reason_id'] = $this->campaign->id;

        return $details;
    }
}

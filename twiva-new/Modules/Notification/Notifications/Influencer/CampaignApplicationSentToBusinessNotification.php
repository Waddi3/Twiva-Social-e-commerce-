<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class CampaignApplicationSentToBusinessNotification extends BaseNotificationClass
{
    public
    array $details;
    private $campaign;


    public function __construct($campaign)
    {
        $this->campaign = $campaign;
        $this->onQueue('campaign-actions');
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_EMAIL_FCM;
    }

    function makeDetails(): array
    {
        $details['title'] =NotificationTitle::select('title')->firstWhere('slug', 'campaign_application_sent_to_business_notification_title')?->title; // 'Your campaign application has been sent to the business.';
        $details['fcm_title'] =NotificationTitle::select('title')->firstWhere('slug', 'campaign_application_sent_to_business_notification_fcm_title')?->title; // 'Campaign application has been sent !';

        $details['sms_message'] = null;
        $details['email_message'] = "Your campaign application has been submitted.";
        $details['flutter_action'] = NotificationEntity::VISIT_INVITATION_ACTION;
        $details['reason_id'] = $this->campaign->id;

        return $details;
    }
}

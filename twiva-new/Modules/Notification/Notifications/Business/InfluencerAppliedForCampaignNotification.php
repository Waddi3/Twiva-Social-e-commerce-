<?php

namespace Modules\Notification\Notifications\Business;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class InfluencerAppliedForCampaignNotification extends BaseNotificationClass
{
    public array $details;
    private $invitation;


    public function __construct($invitation)
    {
        $this->invitation = $invitation;
        $this->onQueue('campaign-actions');
        return parent::__construct($this->makeDetails());

    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_FCM;
    }


    function makeDetails(): array
    {
        $influencerName = $this->invitation->influencer->name;
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'influencer_applied_for_campaign_notification_title')?->title; // 'An influencer has applied to your campaign invitation.';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'influencer_applied_for_campaign_notification_fcm_title')?->title; // 'Invitation Applied.';
        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::VISIT_CAMPAIGN_ACTION;
        $details['reason_id'] = $this->invitation->post->id;

        return $details;
    }
}

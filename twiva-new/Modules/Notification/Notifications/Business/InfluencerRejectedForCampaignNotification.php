<?php

namespace Modules\Notification\Notifications\Business;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class InfluencerRejectedForCampaignNotification extends BaseNotificationClass
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
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'influencer_rejected_for_campaign_notification_title')?->title; // 'An influencer has rejected to your campaign invitation.';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'influencer_rejected_for_campaign_notification_fcm_title')?->title; // 'Invitation rejected ';

        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::VISIT_CAMPAIGN_ACTION;
        $details['reason_id'] = $this->invitation->post->id;

        return $details;
    }
}

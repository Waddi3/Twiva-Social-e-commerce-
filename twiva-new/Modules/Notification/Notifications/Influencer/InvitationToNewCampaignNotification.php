<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;
use Modules\System\Entities\System;

class InvitationToNewCampaignNotification extends BaseNotificationClass
{
    public array $details;
    private $invitation;


    public function __construct($invitation)
    {
        $this->onQueue('campaign-actions');
        $this->invitation = $invitation;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        $via = NotificationEntity::VIA_ALL;
        return self::checkSystem($via);
    }

    function makeDetails(): array
    {
        $clientName = $this->invitation->user->name ?? ' ';
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'invitation_to_new_campaign_notification_title')?->title; // 'You have been invited to a new campaign, apply now';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'invitation_to_new_campaign_notification_fcm_title')?->title; // 'Apply now!';
        $details['sms_message'] = 'You have been invited to a new campaign, apply now';
        $details['email_message'] = "You have been invited by Client $clientName for a influencer marketing campaign. Accept the gig here";
        $details['flutter_action'] = NotificationEntity::VISIT_INVITATION_ACTION;
        $details['reason_id'] = $this->invitation->id;
        return $details;
    }
}

<?php

namespace Modules\Chat\Notifications;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Notifications\BaseNotificationClass;

class ChatTimelineNotificationByEmailAndFcm extends BaseNotificationClass
{
    public array $details;
    private $timeline;
    private $influencer;

    public function __construct($timeline, $influencer)
    {
        $this->timeline = $timeline;
        $this->influencer = $influencer;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        if ($this->influencer->is_active_notification_email && $this->influencer->is_active_notification_fcm) {
            return NotificationEntity::VIA_EMAIL_FCM;
        } elseif ($this->influencer->is_active_notification_email) {
            return NotificationEntity::VIA_EMAIL;
        } elseif ($this->influencer->is_active_notification_fcm) {
            return NotificationEntity::VIA_FCM;
        }
        return [];
    }

    function makeDetails(): array
    {
        $details['title'] = 'New timeline';
        $details['fcm_title'] = "New timeline";

        $details['sms_message'] = null;
        $details['email_message'] = "Check Community Section to see new timeline";
        $details['flutter_action'] = NotificationEntity::VISIT_COMMUNITY_ACTION;
        $details['reason_id'] = $this->timeline->id;

        return $details;
    }
}

<?php

namespace Modules\Chat\Notifications;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Notifications\BaseNotificationClass;

class ChatTimelineNotificationCommentPostByEmailAndFcm extends BaseNotificationClass
{
    public array $details;
    private $timeline;
    private $influencer;
    private $user;

    public function __construct($timeline, $influencer, $user)
    {
        $this->timeline = $timeline;
        $this->influencer = $influencer;
        $this->user = $user;
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
        $details['title'] = 'Add comment';
        $details['fcm_title'] = "Add comment";

        $details['sms_message'] = null;
        $details['email_message'] = "{$this->user->name} add comment on your timeline.";
        $details['flutter_action'] = NotificationEntity::VISIT_COMMUNITY_ACTION;
        $details['reason_id'] = $this->timeline->id;

        return $details;
    }
}

<?php

namespace Modules\Notification\Notifications\Influencer;

use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\Notification\Entities\NotificationTitle;
use Modules\Notification\Notifications\BaseNotificationClass;

class PostIsScheduledNotification extends BaseNotificationClass
{
    public array $details;
    private $post;


    public function __construct($post)
    {
        $this->onQueue('system');
        $this->post = $post;
        return parent::__construct($this->makeDetails());
    }

    public function via($notifiable): array
    {
        return NotificationEntity::VIA_FCM;
    }

    function makeDetails(): array
    {
        $details['title'] = NotificationTitle::select('title')->firstWhere('slug', 'post_is_scheduled_notification_title')?->title; //  'Scheduled post';
        $details['fcm_title'] = NotificationTitle::select('title')->firstWhere('slug', 'post_is_scheduled_notification_fcm_title')?->title; //  'Your post is scheduled.';
        $details['sms_message'] = null;
        $details['email_message'] = null;
        $details['flutter_action'] = NotificationEntity::VISIT_POST_STORY_ACTION;
        $details['reason_id'] = $this->post->id;

        return $details;
    }
}

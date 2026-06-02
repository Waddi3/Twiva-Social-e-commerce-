<?php

namespace Modules\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Notifications\ChatTimelineNotificationByEmailAndFcm;
use Modules\Notification\Entities\Notification as NotificationEntity;
use Modules\User\Entities\User;

class TimelineNotifyByEmailAndFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timeline;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($timeline)
    {
        $this->timeline = $timeline;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        User::where('type', User::InfluencerType)
            // ->where('is_active_notification_email', true)
            // ->where('is_active_notification_fcm', true)
            ->whereNull('de_active_community_at')
            ->whereNull('banned_at')
            ->chunk(100, function ($influencers) {
                foreach ($influencers as $influencer) {
                    $influencer->notify(new ChatTimelineNotificationByEmailAndFcm($this->timeline, $influencer));
                }
            });
    }
}

<?php

namespace Modules\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Notifications\ChatTimelineNotificationLikePostByEmailAndFcm;

class TimelineNotifyLikePostByEmailAndFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timeline;
    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($timeline, $user)
    {
        $this->timeline = $timeline;
        $this->user = $user;
        $this->onQueue('timeline-actions');

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $influencer = $this->timeline->user;
        $influencer->notify(new ChatTimelineNotificationLikePostByEmailAndFcm($this->timeline, $influencer, $this->user));
    }
}

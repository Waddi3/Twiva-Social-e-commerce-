<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Platform\Services\TiktokServices;
use Modules\Platform\Services\TwitterServices;
use Modules\User\Entities\User;

class PostToTiktokJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    private $storyPostID;
    private $influencerID;

    public function __construct($influencerID, $storyPostID)
    {
        $this->storyPostID = $storyPostID;
        $this->influencerID = $influencerID;
        $this->onQueue('social-media-tiktok');

    }

    public function handle()
    {
        $influencer = User::find($this->influencerID);
        if ($influencer)
        {
            TiktokServices::post(User::find($this->influencerID), $this->storyPostID);
        }
    }
}

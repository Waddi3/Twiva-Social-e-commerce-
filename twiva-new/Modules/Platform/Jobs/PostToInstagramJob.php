<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Services\InstagramServices;
use Modules\Platform\Services\SocialMediaServices;
use Modules\User\Entities\User;

class PostToInstagramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    private $storyPostID;
    private $influencerID;

    public function __construct($influencerID, $storyPostID)
    {
        $this->storyPostID = $storyPostID;
        $this->influencerID = $influencerID;
        $this->onQueue('social-media-instagram');

    }

    public function handle()
    {
        $influencer = User::find($this->influencerID);
        if ($influencer) {
            // $story_post = StoryPost::find($this->storyPostID);
            // if($story_post->publish_type){

            // }
            InstagramServices::postToInstagram(User::find($this->influencerID), $this->storyPostID);
            InstagramServices::reelToInstagram($influencer, $this->storyPostID);
            InstagramServices::storyToInstagram($influencer, $this->storyPostID);
        }
    }
}

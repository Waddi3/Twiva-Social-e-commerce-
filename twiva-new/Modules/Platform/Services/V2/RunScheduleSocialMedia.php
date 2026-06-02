<?php

namespace Modules\Platform\Services\V2;

use Modules\Platform\Entities\Platform;
use Modules\Platform\Jobs\PostToFacebookJob;
use Modules\Platform\Jobs\PostToInstagramJob;
use Modules\Platform\Jobs\PostToTiktokJob;
use Modules\Platform\Jobs\PostToTwitterJob;
use Modules\User\Entities\User;

class RunScheduleSocialMedia
{

    protected $story_posts;
    public function __construct($story_posts)
    {
        if (!is_null($story_posts)) {
            if (!is_array($story_posts)) {
                $this->story_posts = [$story_posts];
            } else {
                $this->story_posts = $story_posts;
            }
            $this->runSchedule();
        }
    }

    public function runSchedule()
    {
        if (!is_null($this->story_posts)) {
            foreach ($this->story_posts as $story_post) {
                if ($story_post) {
                    $this->checkPlatform($story_post);
                }
            }
        }
    }

    public function checkPlatform($story_post)
    {
        if ($story_post->hasPlatform(Platform::FacebookID)) {
            PostToFacebookJob::dispatch($story_post->influencer_id, $story_post->id);
        }
        if ($story_post->hasPlatform(Platform::InstagramID)) {
            PostToInstagramJob::dispatch($story_post->influencer_id, $story_post->id);
        }
        if ($story_post->hasPlatform(Platform::TwitterID)) {
            PostToTwitterJob::dispatch($story_post->influencer_id, $story_post->id);
        }
        if ($story_post->hasPlatform(Platform::TikTokID)) {
            PostToTiktokJob::dispatch($story_post->influencer_id, $story_post->id);
        }
    }
}

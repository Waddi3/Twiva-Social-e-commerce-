<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Platform\Services\InstagramServices;

class PublishToInstagramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    private $story;
    private $instagramBusinessID;
    private $accessToken;
    private $influencer;
    private $imageContainers;
    private $try;

    public function __construct($story, $instagramBusinessID, $accessToken, $influencer, $imageContainers, $try = 1)
    {
        $this->story = $story;
        $this->instagramBusinessID = $instagramBusinessID;
        $this->accessToken = $accessToken;
        $this->influencer = $influencer;
        $this->imageContainers = $imageContainers;
        $this->try = $try;
    }

    public function handle()
    {
        $is_published = InstagramServices::checkTheContainerStatus($this->imageContainers, $this->accessToken);
        // InstagramServices::publishToInstagram($this->story, $this->instagramBusinessID, $this->accessToken, $this->influencer, $this->imageContainers);
        if ($is_published) {
            InstagramServices::publishToInstagram($this->story, $this->instagramBusinessID, $this->accessToken, $this->influencer, $this->imageContainers);
        } else {
            $this->try++;
            if ($this->try < 6) {
                PublishToInstagramJob::dispatch($this->story, $this->instagramBusinessID, $this->accessToken, $this->influencer, $this->imageContainers, $this->try);
            }
        }
    }
}

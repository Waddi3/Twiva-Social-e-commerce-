<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Platform\Services\FacebookServices;
use Modules\User\Entities\User;

class PostToFacebookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $storyPostID;

    /**
     * The influencer ID.
     *
     * @var mixed
     */
    private $influencerID;

    /**
     * Create a new job instance.
     *
     * @param mixed $influencerID
     * @param mixed $storyPostID
     * @return void
     */
    public function __construct($influencerID, $storyPostID)
    {
        $this->storyPostID = $storyPostID;
        $this->influencerID = $influencerID;
        $this->onQueue('social-media-facebook');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $influencer = User::find($this->influencerID);

        if ($influencer) {
            FacebookServices::postToFacebook($influencer, $this->storyPostID);
        }
    }
}

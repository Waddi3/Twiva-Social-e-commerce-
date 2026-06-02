<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Platform\Services\V2\FetchScheduleSocialMedia;
use Modules\Platform\Services\V2\RunScheduleSocialMedia;

class RunScheduleSocialMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $story_posts;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($story_posts)
    {
        $this->story_posts = $story_posts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $run_schedule_social_media = new RunScheduleSocialMedia($this->story_posts);
    }
}

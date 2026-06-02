<?php

namespace Modules\Platform\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Platform\Services\V2\FetchScheduleSocialMedia;
use Modules\Platform\Services\V2\RunScheduleSocialMedia;

class ScheduleSocialMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fetch_schedule_social_media = new FetchScheduleSocialMedia();
        $story_posts = $fetch_schedule_social_media->fetchStoryPost();
        RunScheduleSocialMediaJob::dispatch($story_posts);
    }
}

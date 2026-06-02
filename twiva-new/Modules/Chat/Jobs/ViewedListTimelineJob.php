<?php

namespace Modules\Chat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ViewedListTimelineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timelines;
    public $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($timelines, $user_id)
    {
        $this->timelines = $timelines;
        $this->user_id = $user_id;
        $this->onQueue('timeline-actions');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->timelines as $timeline) {
            $view = $timeline->views()->where('user_id', $this->user_id)->first();
            if (!$view) {
                $view = $timeline->views()->create(['user_id' => $this->user_id]);
            }
        }
    }
}

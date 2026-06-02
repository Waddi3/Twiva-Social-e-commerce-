<?php

namespace Modules\Platform\Services\V2;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Platform\Entities\StoryPost;

class FetchScheduleSocialMedia
{

    protected $story_posts;
    public function __construct() {}

    public function fetchStoryPost()
    {
        StoryPost::query()
            ->where('is_schedule', 1)
            ->where('is_completed', 0)
            ->whereNull('canceled_at')
            ->where('number_of_time', '>', 'script_run_count')
            ->where(function ($where) {
                return $where->whereNull('end_date') // WHERE end_date IS NOT NULL
                    ->orWhere('end_date', '>', now());   // OR end_date > NOW()
            })
            ->where('start_date', '<', now())
            // ->count();
            ->chunk(100, function ($story_posts) {
                $this->checkStoryPosts($story_posts);
            });
        return $this->story_posts;
    }

    public function checkStoryPosts($story_posts)
    {
        // dd($story_posts->pluck('start_date'));
        foreach ($story_posts as $story_post) {
            $this->checkStoryPost($story_post);
        }
    }

    public function checkStoryPost($story_post)
    {
        // $end_date = is_null($story_post->end_date) ? null : Carbon::parse($story_post->end_date)->endOfDay();
        // $start_date = Carbon::parse($story_post->start_date);
        $days = null;
        if ($story_post->repeat_type == StoryPost::REPEAT_TYPE_IN_DAYS) {
            $days = explode(',', $story_post->repeated_days);
        }
        $today = Str::lower(now()->dayName);

        if ($days) { // has a specific days
            if ($story_post->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES) {
                if (in_array($today, $days)) {
                    $this->checkTime($story_post); // check time
                }
            }
        } else { // all days
            $this->checkTime($story_post); // check time
        }
        // dd($this->story_posts);
    }

    public function checkTime($story_post)
    {
        $start_time = Carbon::parse($story_post->start_date)->format('H:i');
        $current_time = now()->format('H:i');
        if ($start_time == $current_time) {
            $this->story_posts[] = $story_post;
        }
    }
}

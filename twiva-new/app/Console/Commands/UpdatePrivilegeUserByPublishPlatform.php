<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\Order\Entities\Order;
use Modules\Platform\Entities\StoryPost;
use Modules\User\Entities\User;

class UpdatePrivilegeUserByPublishPlatform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:privileges-by-publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders users and convert it to privilege.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $twoDaysAgo = $now->subDays(200);

        StoryPost::with([
            'user:id,name,email,type,phone_number,phone_code,status,is_verified,is_profile_completed,waive_prompt,privilege',
        ])
            ->where('created_at', '>=', $twoDaysAgo)
            // ->where('is_completed', 1)
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($story_posts) {
                foreach ($story_posts as $story_post) {
                    $count_of_platform = $story_post->platform->count();
                    $privileges = $count_of_platform * config('twiva.privilege.publish-in-platform');
                    $user = $story_post->user;
                    if ($user) $user->increment('privilege', $privileges);
                }
            });
    }
}

<?php

namespace Modules\Platform\Console;

use Illuminate\Console\Command;
use Modules\Influencer\Entities\SocialEngagements;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;

class OldPlatformInformations extends Command
{
    protected $name = 'platform:old';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Getting Info ");
        $socialEngagements = SocialEngagements::all();

        $bar = $this->output->createProgressBar(count($socialEngagements));
        $bar->start();
        foreach ($socialEngagements as $socialEngagment)
        {
            if ($socialEngagment->instagram_last_connected_date && $socialEngagment->instagram_id)
            {
                UserPlatform::create([
                    'social_id' => $socialEngagment->instagram_id,
                    'name' => $socialEngagment->instagram_name,
                    'engagements' => $socialEngagment->instagram_engagements,
                    'followers' => $socialEngagment->instagram_followers,
                    'avg_engagements_per_post' => $socialEngagment->instagram_avg_engagements_per_post,
                    'token' => $socialEngagment->instagram_token,
                    'additional_info' => null,
                    'influencer_id' => $socialEngagment->influencer_id,
                    'platform_id' => Platform::InstagramID,
                    'created_at' => $socialEngagment->created_at,
                    'updated_at' => $socialEngagment->instagram_last_connected_date

                ]);
            }
            if ($socialEngagment->twitter_last_connected_date && $socialEngagment->twitter_id)
            {
                UserPlatform::create([
                    'social_id' => $socialEngagment->twitter_id,
                    'name' => $socialEngagment->twitter_name,
                    'engagements' => $socialEngagment->twitter_engagements,
                    'followers' => $socialEngagment->twitter_followers,
                    'avg_engagements_per_post' => $socialEngagment->twitter_avg_engagements_per_post,
                    'token' => $socialEngagment->twitter_token,
                    'additional_info' => null,
                    'influencer_id' => $socialEngagment->influencer_id,
                    'platform_id' => Platform::TwitterID,
                    'created_at' => $socialEngagment->created_at,
                    'updated_at' => $socialEngagment->twitter_last_connected_date
                ]);

            }
            if ($socialEngagment->facebook_last_connected_date && $socialEngagment->facebook_id)
            {
                UserPlatform::create([
                    'social_id' => $socialEngagment->facebook_id,
                    'name' => $socialEngagment->facebook_name,
                    'engagements' => $socialEngagment->facebook_engagements,
                    'followers' => $socialEngagment->facebook_followers,
                    'avg_engagements_per_post' => $socialEngagment->facebook_avg_engagements_per_post,
                    'token' => $socialEngagment->facebook_token,
                    'additional_info' => null,
                    'influencer_id' => $socialEngagment->influencer_id,
                    'platform_id' => Platform::FacebookID,
                    'created_at' => $socialEngagment->created_at,
                    'updated_at' => $socialEngagment->facebook_last_connected_date
                ]);

            }
            if ($socialEngagment->youtube_last_connected_date && $socialEngagment->youtube_id)
            {
                UserPlatform::create([
                    'social_id' => $socialEngagment->youtube_id,
                    'name' => $socialEngagment->youtube_name,
                    'engagements' => $socialEngagment->youtube_engagements,
                    'followers' => $socialEngagment->youtube_followers,
                    'avg_engagements_per_post' => $socialEngagment->youtube_avg_engagements_per_post,
                    'token' => $socialEngagment->youtube_token,
                    'additional_info' => null,
                    'influencer_id' => $socialEngagment->influencer_id,
                    'platform_id' => Platform::YoutubeID,
                    'created_at' => $socialEngagment->created_at,
                    'updated_at' => $socialEngagment->youtube_last_connected_date
                ]);

            }
            $bar->advance();
        }
        $bar->finish();


    }

}

<?php

namespace Modules\Platform\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Entities\Platform;
use GuzzleHttp\Client;
use Modules\User\Entities\User;

class  TiktokServices
{
    public static function post(User $influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $influencerTiktok = $influencer->UserPlatforms()->where('platform_id', Platform::TikTokID)->first();
        if (!$influencerTiktok) {
            return false;
        }
        if (!$story->canceled_at) {

            try {

                $client = new Client(
                    [
                        'base_uri' => 'https://open.tiktokapis.com/',
                        'headers' => [
                            'Authorization' => "Bearer {$influencerTiktok->token}",
                            'Content-Type' => 'application/json'
                        ],
                    ]
                );

                $response = $client->post('v2/post/publish/content/init/', [
                    RequestOptions::JSON => [
                        'post_info' => [
                            'title' => $story->caption,
                            'description' => $story->caption,
                            // new
                            "disable_comment" => true,
                            "privacy_level" => "PUBLIC_TO_EVERYONE", // ["PUBLIC_TO_EVERYONE", "MUTUAL_FOLLOW_FRIENDS", "SELF_ONLY"]
                            // "auto_add_music" => true
                        ],
                        'source_info' => [
                            'source' => 'PULL_FROM_URL',
                            'photo_cover_index' => 1,
                            'photo_images' => [
                                asset('storage/images/' . $story->image)
                            ]
                        ],
                        "post_mode" => "DIRECT_POST",
                        "media_type" => "PHOTO",
                    ]
                ]);

                $result = json_decode($response->getBody()->getContents());
                if ($response->getStatusCode() === 200 && isset($result['data']['publish_id'])) {
                    // Save following lines
                    $story->update(['script_run_count' => ++$story->script_run_count]);
                    $story = $story->fresh();
                    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_DATE) && (!Carbon::parse($story->end_date)->diffInDays(now()))) {
                        $story->update(['is_completed' => '1']);
                    }
                    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES) && ($story->script_run_count == $story->number_of_time)) {
                        $story->update(['is_completed' => '1']);
                    }

                    return true;
                }
                return false;
            } catch (Exception $exception) {
                Log::error("error when posting to Tiktok ", ['error' => $exception->getMessage()]);
            }
        }
        return true;
    }
}

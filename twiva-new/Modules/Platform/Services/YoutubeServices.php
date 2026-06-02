<?php


namespace Modules\Platform\Services;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;

class  YoutubeServices
{
    public static function getYouTubeActivities($influencer)
    {
        $client = new Client();
        if ($influencer?->UserPlatforms()->where('platform_id', Platform::YoutubeID)->count())
        {
            try
            {
                $request = $client->request('GET', 'https://youtube.googleapis.com/youtube/v3/channels',
                    [
                        'query' => [
                            'mine' => "true",
                            'part' => 'statistics',
                            'key' => config('engagements.youtube_api_key'),
                        ],
                        'headers' =>
                            [
                                'Authorization' => "Bearer {$influencer->UserPlatforms()->where('platform_id', Platform::YoutubeID)->value('token')}"
                            ]
                    ]
                );
                $contents = json_decode($request->getBody()->getContents());
                if (isset($contents->items[0]))
                {
                    $result = $contents->items[0]->statistics;
                    $avg_engagements_per_post = $result->videoCount == 0 ? 0 : ($result->viewCount / $result->videoCount);
                    UserPlatform::updateOrCreate(
                        ['influencer_id' => $influencer->id, 'platform_id' => Platform::YoutubeID],
                        [
                            'engagements' => $result->viewCount,
                            'avg_engagements_per_post' => (int)$avg_engagements_per_post,
                            'followers' => $result->subscriberCount,
                        ]);
                }
            }
            catch (Exception|GuzzleException)
            {
                UserPlatform::updateOrCreate(
                    ['influencer_id' => $influencer->id, 'platform_id' => Platform::YoutubeID],
                    [
                        'token' => null
                    ]);
            }
        }
    }
}

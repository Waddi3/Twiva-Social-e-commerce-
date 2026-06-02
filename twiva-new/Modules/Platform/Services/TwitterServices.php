<?php


namespace Modules\Platform\Services;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use GuzzleHttp\Client;
use Modules\User\Entities\User;
use function Symfony\Component\Translation\t;

class  TwitterServices
{
    public static function getTwitterActivities($influencer)
    {
        if ($influencer->UserPlatforms()->where('platform_id', Platform::TwitterID)->count()) {
            $tokenAndSecret = explode('=>', $influencer->UserPlatforms()->where('platform_id', Platform::TwitterID)->first()->token);
            try {
                $userFromTwitter = Socialite::driver('twitter')->userFromTokenAndSecret($tokenAndSecret[0], $tokenAndSecret[1]);
                UserPlatform::updateOrCreate(
                    ['influencer_id' => $influencer->id, 'platform_id' => Platform::TwitterID],
                    [
                        'followers' => $userFromTwitter->user['followers_count'],
                        'token' => $userFromTwitter->token . "=>" . $userFromTwitter->tokenSecret,
                        'engagements' => 0, // N/A waiting for Enterprise Access
                    ]
                );
            } catch (Exception $exception) {
                Log::debug('Social Medial Services Twitter error 127', ['detail' => $exception, 'User' => $influencer]);
                UserPlatform::updateOrCreate(
                    ['influencer_id' => $influencer->id, 'platform_id' => Platform::TwitterID],
                    ['token' => null]);
            }
        }
    }

    public static function tweet(User $influencer, $storyID, $is_video = false): bool
    {
        $story = StoryPost::find($storyID);
        $influencerTwitter = $influencer->UserPlatforms()->where('platform_id', Platform::TwitterID)->first();
        if (!$influencerTwitter) {
            return false;
        }
        if (!$story->canceled_at) {

            $twitter_access_token = $influencerTwitter->additional_info["token"] ?? "";
            try {
                // logger("Modules/Platform/Services/TwitterServices.php => ", ['is_video' => $is_video]);
                if ($is_video) {
                    $files = $story->absolute_images->implode(' ');
                    $buy_link = InstagramServices::prepareBuyLink($story, $influencer);
                    $video = generateVideo($files, $buy_link, false, true);
                    $mediaIds = self::uploadVideoMedia($video, $twitter_access_token, $influencerTwitter->token);
                    logger("after video id", ['media ids' => $mediaIds, 'video' => $video]);
                } else {
                    $mediaIds = self::uploadMedia($story->image, $twitter_access_token, $influencerTwitter->token);
                }

                if ($mediaIds) {
                    $stack = HandlerStack::create();
                    $middleware = new Oauth1([
                        'consumer_key' => config('services.twitter.client_id'),
                        'consumer_secret' => config('services.twitter.client_secret'),
                        'token' => $twitter_access_token,
                        'token_secret' => $influencerTwitter->token
                    ]);
                    $stack->push($middleware);

                    // logger("media ids", ['type' => gettype($mediaIds)]);
                    // logger("media ids", ['value' => $mediaIds]);

                    sleep(5);

                    $client = new Client([
                        'base_uri' => 'https://api.twitter.com/',
                        'handler' => $stack,
                        'auth' => 'oauth',
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'text' => substr($story->caption, 0, 255),
                            'media' => [
                                'media_ids' => $mediaIds
                            ],
                        ],
                    ]);

                    $response = $client->post('2/tweets');

                    /*$client = new Client(
                        [
                            'base_uri' => 'https://api.twitter.com/',
                            'handler' => $stack,
                            'auth' => 'oauth',
                            'headers' => [
                                'Content-Type' => 'application/json',
                            ],
                        ]);*/

                    /*$response = $client->post('2/tweets', [
                        \GuzzleHttp\RequestOptions::JSON => [
                            'text' => $story->caption, // InstagramServices::prepareCaption($story, $influencer),
                            'media' => [
                                'media_ids' => $mediaIds
                            ],
                        ]
                    ]);*/

                    // logger("tweet response", ['status code' => $response->getStatusCode()]);
                    // if( $response->getStatusCode() >= 200 ) {
                    //     logger("tweet response", ['body' => $response->getBody()->getContents()]);
                    // }

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
                Log::error("error when posting to twitter ", [
                    'storyID' => $storyID,
                    'error' => $exception->getMessage(),
                    'influencer' => $influencer,
                ]);
            }
        }
        return true;
    }

    public static function uploadMedia($media, $token, $secret)
    {

        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key' => config('services.twitter.client_id'),
            'consumer_secret' => config('services.twitter.client_secret'),
            'token' => $token,
            'token_secret' => $secret
        ]);
        $stack->push($middleware);

        $results = [];
        $medias = explode(',', $media);
        foreach ($medias as $image) {
            if (Str::of($image)->length()) {
                $data = file_get_contents(storage_path("app/public/$image"));

                $client = new Client([
                    'base_uri' => 'https://upload.twitter.com/1.1/',
                    'handler' => $stack,
                    'multipart' => [
                        [
                            'name' => 'media',
                            'contents' => $data,
                        ],
                    ],
                ]);

                $response = $client->post('media/upload.json?media_category=tweet_image', ['auth' => 'oauth']);
                if ($response->getStatusCode() === 200) {
                    $result = json_decode($response->getBody()->getContents());
                    try {
                        $results[] = $result->media_id_string;
                    } catch (Exception $exception) {

                    }
                }
            }
        }
        return $results;
    }

    public static function uploadVideoMedia($media, $token, $secret)
    {
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key' => config('services.twitter.client_id'),
            'consumer_secret' => config('services.twitter.client_secret'),
            'token' => $token,
            'token_secret' => $secret
        ]);
        $stack->push($middleware);

        $results = [];
        $videoPath = storage_path("app/public/$media");
        // logger("Video path", ['path' => $videoPath]);
        $videoSize = filesize($videoPath);
        // logger("Video size", ['size' => $videoSize]);
        if( ($videoSize / 1048576) > 512 ) {
            return [];
        }
        // logger('check video path before uploading in twitter', ['videoPath' => $videoPath]);
        $data = file_get_contents($videoPath);

        $initRequestClient = new Client([
            'base_uri' => 'https://upload.twitter.com/1.1/',
            'handler' => $stack,
            'form_params' => [
                'command' => 'INIT',
                'media_type' => 'video/mp4',
                'total_bytes' => $videoSize
            ],
        ]);

        $initRequestResponse = $initRequestClient->post('media/upload.json', ['auth' => 'oauth']);
        if ($initRequestResponse->getStatusCode() === 202) {
            $initRequestResult = json_decode($initRequestResponse->getBody()->getContents());
            if( !$initRequestResult ) {
                return $results;
            }

            // logger("init video", ['response' => $initRequestResult]);
            // logger("Init video id", ['media id' => $initRequestResult->media_id_string]);

            $appendRequestClient = new Client([
                'base_uri' => 'https://upload.twitter.com/1.1/',
                'handler' => $stack,
                'multipart' => [
                    [
                        'name' => 'command',
                        'contents' => 'APPEND',
                    ],
                    [
                        'name' => 'media_id',
                        'contents' => $initRequestResult->media_id_string,
                    ],
                    [
                        'name' => 'segment_index',
                        'contents' => 0,
                    ],
                    [
                        'name' => 'media',
                        'contents' => $data,
                    ],
                ],
            ]);
            $appendResponse = $appendRequestClient->post('media/upload.json', ['auth' => 'oauth']);

            // logger("append", ['response status' => $appendResponse->getStatusCode()]);
            if( $appendResponse->getStatusCode() >= 200 ) {
                // logger("append", ['response' => json_decode($appendResponse->getBody()->getContents())]);
                $finalizeRequestClient = new Client([
                    'base_uri' => 'https://upload.twitter.com/1.1/',
                    'handler' => $stack,
                    'multipart' => [
                        [
                            'name' => 'command',
                            'contents' => 'FINALIZE',
                        ],
                        [
                            'name' => 'media_id',
                            'contents' => $initRequestResult->media_id_string,
                        ],
                    ],
                ]);
                $finalizeResponse = $finalizeRequestClient->post('media/upload.json', ['auth' => 'oauth']);
                // logger("finalize", ['response status' => $finalizeResponse->getStatusCode()]);
                if( $finalizeResponse->getStatusCode() == 200 ) {
                    $finalizeResult = json_decode($finalizeResponse->getBody()->getContents());
                    logger("finalize", ['result' => $finalizeResult]);
                    if( !$finalizeResult ) {
                        return $results;
                    }
                    $results[] = $finalizeResult->media_id_string;
                }
            }
        }

        return $results;
    }
}

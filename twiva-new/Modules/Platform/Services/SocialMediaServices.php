<?php


namespace Modules\Platform\Services;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;

class  SocialMediaServices
{

    public const version = 'v20.0';

    public static function getYouTubeActivities($influencer)
    {

        $client = new Client();
        if ($influencer->UserPlatforms()->where('platform_id', Platform::YoutubeID)->count()) {
            try {
                $request = $client->request(
                    'GET',
                    'https://youtube.googleapis.com/youtube/v3/channels',
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
                if (isset($contents->items[0])) {
                    $result = $contents->items[0]->statistics;
                    $avg_engagements_per_post = $result->videoCount == 0 ? 0 : ($result->viewCount / $result->videoCount);
                    UserPlatform::updateOrCreate(
                        ['user_id' => $influencer->id, 'platform_id' => Platform::YoutubeID],
                        [
                            'engagements' => $result->viewCount,
                            'avg_engagements_per_post' => (int)$avg_engagements_per_post,
                            'followers' => $result->subscriberCount,
                        ]
                    );
                }
            } catch (Exception | GuzzleException) {
                UserPlatform::updateOrCreate(
                    ['user_id' => $influencer->id, 'platform_id' => Platform::YoutubeID],
                    [
                        'token' => null
                    ]
                );
            }
        }
    }

    private static function getInfluencerFacebookPages($influencer)
    {
        if ($pageToken = $influencer?->UserPlatforms()?->where('platform_id', Platform::FacebookID)?->value('token') ?? false) {
            return false;
        }
        Log::debug('$pageToken', ['$pageToken' => $pageToken]);

        $client = new Client();
        $request = '';
        try {
            $influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->value('token');
            $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/me/accounts', [
                'query' => [
                    'access_token' => $pageToken,
                ]
            ]);
        } catch (GuzzleException $exception) {
            Log::debug('FIX:Call to a member function getBody() on string', ['$exception' => $exception]);
            return false;
        }
        return json_decode($request->getBody()->getContents())->data;
    }

    private static function getInfluencerInstagramPages($influencer)
    {
        $client = new Client();
        $request = '';
        try {
            $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/me/accounts', [
                'query' => [
                    'access_token' => $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->value('token'),
                ]
            ]);
        } catch (GuzzleException $exception) {
            Log::error('error when getInfluencerInstagramPages Modules/Platform/Services/SocialMediaServices.php:105 => ', ['error' => $exception]);
        }
        return json_decode($request->getBody()->getContents())->data;
    }

    public static function getFaceBookActivities($influencer)
    {
        if (($influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->count())) {
            try {
                $pages = self::getInfluencerFacebookPages($influencer);
                if ($pages) {
                    $engagments = 0;
                    $followers = 0;
                    $avg_engagements_per_post = 0;
                    $client = new Client();
                    foreach ($pages as $page) {
                        $request = $client->request('GET', 'https://graph.facebook.com/' . $page->id . '/', [
                            'query' => [
                                'access_token' => $page->access_token,
                                'fields' => 'engagement,followers_count,published_posts'
                            ]
                        ]);
                        $result = json_decode($request->getBody()->getContents());
                        $engagments += (isset($result->engagement) ? $result->engagement->count : 0);
                        $followers += $result->followers_count ?? 0;
                        $published_posts = isset($result->published_posts) ? count((array)$result->published_posts->data) : 1;
                        $avg_engagements_per_post += ($engagments / ($published_posts ?? 1)) ?? 0;
                    }
                    UserPlatform::updateOrCreate(
                        ['user_id' => $influencer->id, 'platform_id' => Platform::FacebookID],
                        [
                            'engagements' => $engagments,
                            'followers' => $followers,
                            'avg_engagements_per_post' => $avg_engagements_per_post,
                        ]
                    );
                }
            } catch (Exception | GuzzleException $exception) {
                Log::debug('Social Medial Services error 103 Modules/Platform/Services/SocialMediaServices.php:149 => ', ['detail' => $exception, 'User' => $influencer]);
                UserPlatform::updateOrCreate(
                    ['user_id' => $influencer->id, 'platform_id' => Platform::FacebookID],
                    ['token' => null]
                );
            }
        }
    }

    public static function getTwitterActivities($influencer)
    {
        if ($influencer->UserPlatforms()->where('platform_id', Platform::TwitterID)->count()) {
            $tokenAndSecret = explode('=>', $influencer->UserPlatforms()->where('platform_id', Platform::TwitterID)->first()->token);
            try {
                $userFromTwitter = Socialite::driver('twitter')->userFromTokenAndSecret($tokenAndSecret[0], $tokenAndSecret[1]);
                UserPlatform::updateOrCreate(
                    ['user_id' => $influencer->id, 'platform_id' => Platform::TwitterID],
                    [
                        'followers' => $userFromTwitter->user['followers_count'],
                        'token' => $userFromTwitter->token . "=>" . $userFromTwitter->tokenSecret,
                        'engagements' => 0, // N/A waiting for Enterprise Access
                    ]
                );
            } catch (Exception $exception) {
                Log::debug('Social Medial Services Twitter error 127', ['detail' => $exception, 'User' => $influencer]);

                UserPlatform::updateOrCreate(
                    ['user_id' => $influencer->id, 'platform_id' => Platform::TwitterID],
                    ['token' => null]
                );
            }
        }
    }

    public static function getLongLifeTokenForInstagramAndFacebook($token)
    {
        try {
            $client = new Client();
            $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/oauth/access_token', [
                'query' => [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => config('services.facebook.client_id'),
                    'client_secret' => config('services.facebook.client_secret'),
                    'fb_exchange_token' => $token,
                ]
            ]);
            $contents = json_decode($request->getBody()->getContents());
            return $contents->access_token;
        } catch (Exception | GuzzleException) {
        }
        return false;
    }


    public static function getInstagramActivities($influencer)
    {
        if (($influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->count())) {
            try {
                $instagram_business_account = self::getInstagramBusinessID($influencer);
                if ($instagram_business_account) {
                    self::getInstagramBasicInfo($influencer, $instagram_business_account);
                    //                    self::getInstagramEngagements($influencer, $instagram_business_account);
                    self::getInstagramActivitiesFromScrapper($influencer);
                }
            } catch (Exception $exception) {
                Log::emergency($exception);
            }
        }
    }

    private static function getInstagramActivitiesFromScrapper($influencer)
    {
        try {
            $instagramAccount = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first();
            $client = new Client();
            $request = $client->request(
                'POST',
                'https://business.notjustanalytics.com/analyze/getProfileInfo',
                [
                    'form_params' => [
                        'username' => $instagramAccount->social_id,
                        'type' => 'free',
                        'profile' => 'none'
                    ]
                ]
            );
            $contents = json_decode($request->getBody()->getContents());
            $engagments = ($contents->like_on_comment ?? 0) + ($contents->check_like_var ?? 0) + ($contents->avg_comment ?? 0);

            $media_count = $contents->media_count ?? 1;
            $media_count = $media_count == 0 ? 1 : $media_count;
            $instagramAccount->update(
                [
                    'followers' => $contents->follower ?? 0,
                    'engagements' => $engagments,
                    'avg_engagements_per_post' => $engagments / $media_count,
                ]
            );
        } catch (Exception $exception) {
            Log::debug('Social Medial Services Instagram error 138', ['detail' => $exception, 'User' => $influencer]);
        }
    }

    public static function getInstagramBusinessID($influencer)
    {
        $client = new Client();
        $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/me/accounts', [
            'query' => [
                'access_token' => $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->value('token'),
            ]
        ]);
        $contents = json_decode($request->getBody()->getContents());
        if (isset($contents->data[0])) {
            $page_access_token = $contents->data[0]->access_token;
            $page_id = $contents->data[0]->id;
            $request = $client->request('GET', 'https://graph.facebook.com/' . $page_id . '/', [
                'query' => [
                    'access_token' => $page_access_token,
                    'fields' => 'instagram_business_account'
                ]
            ]);
            $result = json_decode($request->getBody()->getContents());
            return $result->instagram_business_account->id ?? false;
        }
        return false;
    }

    private static function getInstagramBasicInfo($influencer, $instagram_business_account)
    {
        $client = new Client();
        $request = $client->request('GET', 'https://graph.facebook.com/' . $instagram_business_account . '/', [
            'query' => [
                'access_token' => $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->value('token'),
                'fields' => 'username,name'
            ]
        ]);
        $contents = json_decode($request->getBody()->getContents());
        UserPlatform::updateOrCreate(
            ['user_id' => $influencer->id, 'platform_id' => Platform::InstagramID],
            [
                'social_id' => $contents->username,
                'name' => $contents->name,
                //                'followers' => $contents->followers_count,
            ]
        );
    }

    private static function getInstagramEngagements($influencer, $instagram_business_account)
    {
        $client = new Client();
        $userName = $influencer->socialEngagements->instagram_id;
        $request = $client->request('GET', 'https://graph.facebook.com/v3.2/' . $instagram_business_account, [
            'query' => [
                'fields' => "business_discovery.username($userName){followers_count,media_count,media{comments_count,like_count}}",
                'access_token' => $influencer->socialEngagements->instagram_token,
            ]
        ]);
        $contents = json_decode($request->getBody()->getContents())->business_discovery;
        $total_engagements = 0;
        $total_posts = $contents->media_count;
        foreach ($contents->media->data as $post) {
            $total_engagements += ($post->comments_count + $post->like_count);
        }
        $influencer->socialEngagements->update([
            'instagram_engagements' => $total_engagements,
            'instagram_avg_engagements_per_post' => $total_engagements / $total_posts,
        ]);
    }


    public static function postToFacebook($influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        if (!$story->canceled_at) {
            $influencerFacebookPages = self::getInfluencerFacebookPages($influencer);
            Log::debug('$influencerFacebookPages', ['$influencerFacebookPages' => $influencerFacebookPages]);
            if ($influencerFacebookPages) {
                $accessToken = $influencerFacebookPages[0]->access_token;
                $client = new  Client();
                $imageContainers = [];
                try {
                    $buyLink = route('shop.product.show', ['account' => $influencer->shop->domain, 'product' => $story->product_id]);
                    $buyObject = json_encode(['type' => 'SHOP_NOW', 'value' => ['link' => $buyLink]]);
                    $client->request(
                        'POST',
                        'https://graph.facebook.com/me/feed',
                        [
                            'form_params' => [
                                'message' => "$story->caption", // https://peter.twiva.test/888/show
                                'access_token' => $accessToken,
                                'call_to_action' => $buyObject,
                            ]
                        ]
                    );

                    $story->update(['script_run_count' => ++$story->script_run_count]);
                    $story = $story->fresh();
                    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_DATE) && (!Carbon::parse($story->end_date)->diffInDays(now()))) {
                        $story->update(['is_completed' => '1']);
                    }
                    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES) && ($story->script_run_count == $story->number_of_time)) {
                        $story->update(['is_completed' => '1']);
                    }
                } catch (Exception $exception) {
                    Log::error('error when posting to facebook https://graph.facebook.com/me/feed', ['error' => $exception->getMessage(), '$imageContainers' => $imageContainers]);
                }
                return true;
            }
        }
        return true;
    }

    public static function postToInstagram($influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $instagramBusinessID = self::getInstagramBusinessID($influencer);
        $accessToken = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first()->access_token;
        if (count($story->images) > 1) {
            self::InstagramMultiImagePost($story, $instagramBusinessID, $accessToken);
        } else {
            self::InstagramSingleImagePost($story, $instagramBusinessID, $accessToken);
        }

        return true;
    }

    private static function InstagramMultiImagePost($story, $instagramBusinessID, $accessToken): void
    {
        $imageContainers = [];
        $client = new Client();

        foreach ($story->images as $image) {
            try {
                if (Str::contains($image, '.png')) {
                    $request = $client->request(
                        'POST',
                        "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                        [
                            'form_params' => [
                                'access_token' => $accessToken,
                                'image_url' => $image,
                                'is_carousel_item' => 'true',
                            ]
                        ]
                    );
                    $imageContainers[] = json_decode($request->getBody())->id;
                }
            } catch (Exception | GuzzleException $exception) {
                Log::error('1- error when posting to instagram', ['error' => $exception, 'images' => $story->images]);
            }
        }
        if ($imageContainers) {
            try {
                $request = $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'media_type' => 'CAROUSEL',
                            'caption' => $story->caption,
                            'children' => $imageContainers
                        ]
                    ]
                );
                $creationID = json_decode($request->getBody())->id;
            } catch (Exception | GuzzleException $exception) {
                Log::error('2- error when posting to instagram', ['error' => $exception, '$imageContainers' => $imageContainers]);
            }

            try {
                $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media_publish",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'creation_id' => $creationID,
                        ]
                    ]
                );
            } catch (Exception | GuzzleException $exception) {
                Log::error('3- error when posting to instagram', ['error' => $exception, '$creationID' => $creationID]);
            }
        }
    }

    private static function InstagramSingleImagePost($story, $instagramBusinessID, $accessToken): void
    {
        $client = new Client();
        $imageContainers = '';
        try {
            $image = $story->images[0];
            Log::debug('$image is', [
                '$image' => $image,
            ]);
            if (Str::contains($image, '.png')) {
                $request = $client->request(
                    'POST',
                    "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'image_url' => $image,
                            'caption' => $story->caption,
                        ]
                    ]
                );
                $imageContainers = json_decode($request->getBody())->id;
            }
        } catch (Exception | GuzzleException $exception) {
            Log::error('InstagramSingleImagePost 1- error when posting to instagram', ['error' => $exception, 'images' => $story->images]);
        }
        if ($imageContainers) {
            try {
                $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media_publish",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'creation_id' => $imageContainers,
                        ]
                    ]
                );
            } catch (Exception | GuzzleException $exception) {
                Log::error('2- error when posting to instagram', ['error' => $exception, '$imageContainers' => $imageContainers]);
            }
        }
    }
}

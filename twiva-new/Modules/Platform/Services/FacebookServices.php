<?php


namespace Modules\Platform\Services;


use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\User\Entities\User;

class  FacebookServices
{
    public const version = 'v20.0';

    public static function connectToFacebook(User $influencer): bool
    {
        $influencerFacebookPage = self::getInfluencerFacebookPages($influencer);
        if ($influencerFacebookPage) {
            self::getFaceBookActivities($influencer);
            return true;
        } else {
            $influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->update(['token' => null]);
        }
        return false;
    }

    private static function getInfluencerFacebookPages(User $influencer): bool
    {
        $facebookPlatform = $influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->first();
        if (!$facebookPlatform->token) {
            return false;
        }


        $client = new Client();
        try {
            $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/me/accounts', [
                'query' => [
                    'access_token' => $facebookPlatform->token,
                    'fields' => 'username,access_token,id',
                ]
            ]);
        } catch (GuzzleException $exception) {
            Log::debug('Facebook response error while getting pages', ['$exception' => $exception->getMessage()]);
            return false;
        }
        if ($facebookPageData = json_decode($request->getBody()->getContents())?->data[0] ?? false) {
            $facebookPlatform->update(
                [
                    'additional_info' => json_encode(['username' => ($facebookPageData?->username ?? $facebookPageData?->id), 'id' => $facebookPageData?->id ?? null]),
                    'token' => $facebookPageData->access_token,
                ]
            );

            return true;
        }
        return false;
    }

    public static function getFaceBookActivities(User $influencer): void
    {
        $facebookPage = $influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->first();
        $additionalInfo = json_decode($facebookPage->additional_info);
        if ($facebookPage?->token && $additionalInfo->id) {
            try {
                $engagements = 0;
                $followers = 0;
                $avg_engagements_per_post = 0;
                $client = new Client();
                $request = $client->request('GET', 'https://graph.facebook.com/' . $additionalInfo->id . '/', [
                    'query' => [
                        'access_token' => $facebookPage->token,
                        'fields' => 'engagement,followers_count,published_posts'
                    ]
                ]);
                $result = json_decode($request->getBody()->getContents());
                // Log::debug('2- getFaceBookActivities', ['getFaceBookActivities' => $result]);
                $engagements += (isset($result->engagement) ? $result->engagement->count : 0);
                $followers += $result->followers_count ?? 0;
                $published_posts = isset($result->published_posts) ? count((array)$result->published_posts->data) : 1;
                $avg_engagements_per_post += ($engagements / ($published_posts ?? 1)) ?? 0;
                $facebookPage->update(
                    [
                        'engagements' => $engagements,
                        'followers' => $followers,
                        'avg_engagements_per_post' => $avg_engagements_per_post,
                        'token' => MetaServices::getLongLifeToken($facebookPage->token)
                    ]
                );
            } catch (Exception | GuzzleException $exception) {
                Log::debug('Modules/Platform/Services/FacebookServices.php:96', ['detail' => $exception, 'User' => $influencer]);
                $facebookPage->update(['token' => null]);
            }
        } else {
            $facebookPage->update(['token' => null]);
        }
    }

    public static function postToFacebook(User $influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $influencerFacebookPage = $influencer->UserPlatforms()->where('platform_id', Platform::FacebookID)->first();
        $accessToken = $influencerFacebookPage?->token;
        if (!$accessToken) {
            return false;
        }
        if (!$story->canceled_at) {
            if ($influencerFacebookPage) {
                $client = new  Client();
                try {
                    $buyLink = route('shop.product.show', ['account' => $influencer->shop->domain, 'product' => $story->product_id]);
                    $buyObject = json_encode(['type' => 'SHOP_NOW', 'value' => ['link' => $buyLink]]);
                    $client->request(
                        'POST',
                        'https://graph.facebook.com/me/feed',
                        [
                            'form_params' => [
                                'message' => "$story->caption",
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
                    Log::error("error when posting to facebook Modules/Platform/Services/FacebookServices.php:138 ", ['story_id' => $storyID, 'error' => $exception->getMessage()]);
                    $influencerFacebookPage->update(['token' => null]);
                }
                return true;
            }
        }
        return true;
    }
}

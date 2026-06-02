<?php


namespace Modules\Platform\Repositories;


use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Services\FacebookServices;
use Modules\Platform\Services\InstagramServices;
use Modules\Platform\Services\MetaServices;
use Modules\Platform\Services\SocialMediaServices;

class PlatformRepository
{
    public static function storeInstagram($user, $userFromInstagram): bool
    {
        try
        {
            $influencerPlatform = UserPlatform::updateOrCreate(
                ['user_id' => $user->id, 'platform_id' => Platform::InstagramID],
                [
                    'social_id' => $userFromInstagram->getId(),
                    'name' => $userFromInstagram->name ?? $userFromInstagram->getNickname(),
                    'token' => $userFromInstagram->token,
                ]
            );
            list($instagramID, $pageToken) = InstagramServices::getInstagramBusinessID($user);
            if ($instagramID && $pageToken)
            {
                $influencerPlatform->update(['token' => $pageToken, 'additional_info' => json_encode(['business_id' => $instagramID])]);
//                dispatch(function () use ($user) {
//                    InstagramServices::getInstagramActivities($user);
//                })->delay(5);
                return true;
            }
            else
            {
                $influencerPlatform->delete();
                return false;
            }
        }
        catch (Exception  $exception)
        {
            Log::debug('error storeInstagram 47: ', [
                'userFromInstagram' => $userFromInstagram,
                'user' => $user,
                'error' => $exception
            ]);
            return false;
        }
    }

    public static function storeFacebook($user, $userFromFacebook): bool
    {
        $influencerPlatform = UserPlatform::updateOrCreate(
            ['user_id' => $user->id, 'platform_id' => Platform::FacebookID],
            [
                'token' => MetaServices::getLongLifeToken($userFromFacebook->token),
                'social_id' => $userFromFacebook->id,
                'name' => $userFromFacebook->name,
            ]
        );
        dispatch(function () use ($user) {
            FacebookServices::connectToFacebook($user);
        })->delay(3);
        return true;
    }

    public static function storeYouTube($user, $userFromYoutube): bool
    {
        UserPlatform::updateOrCreate(
            ['user_id' => $user->id, 'platform_id' => Platform::YoutubeID],
            [
                'token' => $userFromYoutube->token,
                'social_id' => $userFromYoutube->id,
                'name' => $userFromYoutube->nickname,
            ]
        );
        dispatch(function () use ($user) {
            SocialMediaServices::getYouTubeActivities($user);
        })->delay(5);
        return true;

    }

    public static function storeTwitter($user, $userFromTwitter): bool
    {
        UserPlatform::updateOrCreate(
            ['user_id' => $user->id, 'platform_id' => Platform::TwitterID],
            [
                'social_id' => $userFromTwitter->id,
                'name' => $userFromTwitter->name ?? $userFromTwitter->nickname,
                'token' => $userFromTwitter->token . "=>" . $userFromTwitter->tokenSecret,
                'followers' => $userFromTwitter->user['followers_count'],
                'engagements' => 0, // N/A waiting for Enterprise Access
            ]
        );
        return true;

    }

}

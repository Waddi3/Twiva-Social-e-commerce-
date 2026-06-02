<?php


namespace Modules\Notification\Repository;


class FCMRepository
{
    public static function storeFCM($user, $token)
    {
        $user_tokens = $user->fcm_token ?? [];
        if (!in_array($token, $user_tokens))
        {
            array_push($user_tokens, $token);
            $user->update([
                'fcm_token' => $user_tokens
            ]);
        }

    }

}
<?php

namespace Modules\Platform\Http\Controllers\API\V2;

use Modules\Platform\Entities\Platform;

class ApiV2TikTokPlatformController
{
    public function __construct() {}

    public function index()
    {
        $user = auth('api')->user()->load('UserPlatforms');
        $tiktoks = $user->UserPlatforms->where('platform_id', Platform::TikTokID);
        $data = [];
        if ($tiktoks->isNotEmpty()) {
            foreach ($tiktoks as $tiktok) {
                $data[] = [
                    'user_platform_id' => $tiktok->id,
                    'social_id' => $tiktok->social_id,
                    'name' => $tiktok->name,
                    'additional_info' => [
                        "name" => $tiktok->additional_info['name'] ?? null,
                        "user" => [
                            "username" => $tiktok->additional_info['user']['username'] ?? null,
                            "display_name" => $tiktok->additional_info['user']['display_name'] ?? null,
                            "avatar_large_url" => $tiktok->additional_info['user']['avatar_large_url'] ?? null,
                        ],
                        "email" => $tiktok->additional_info['email'] ?? null,
                        "avatar" => $tiktok->additional_info['avatar'] ?? null,
                        "nickname" => $tiktok->additional_info['nickname'] ?? null,
                    ],
                ];
            }
        }
        return response()->json($data);
    }
}

<?php

namespace Modules\Chat\Http\Repositories\Api\V1;

use Modules\Chat\Entities\Timeline;
use Modules\Chat\Entities\View;

class ApiChatSettingsRepository
{
    public function __construct() {}


    public function settings()
    {
        $user = auth('api')->user();
        $count_of_not_read_timelines = Timeline::query()
            ->select('id', 'user_id')
            ->whereNull('chat_group_id')
            ->where('user_id', '<>', $user->id)
            ->whereNotIn('id', View::select('timeline_id')->where('user_id', $user->id)->pluck('timeline_id'))
            ->count();
        return [
            'is_active_notification_email' => $user->is_active_notification_email,
            'is_active_notification_fcm' => $user->is_active_notification_fcm,
            'de_active_community_at' => $user->de_active_community_at,
            'banned_at' => $user->banned_at,
            'count_of_not_read_timelines' => $count_of_not_read_timelines,
            'has_groups' => (bool)$user->chatGroups()->count(),
        ];
    }

    public function update($data)
    {
        $user = auth('api')->user();

        if (array_key_exists('is_active_notification_email', $data) && !is_null($data['is_active_notification_email'])) {
            $result = $user->update([
                'is_active_notification_email' => $data['is_active_notification_email'],
            ]);
        }

        if (array_key_exists('is_active_notification_fcm', $data) && !is_null($data['is_active_notification_fcm'])) {
            $result = $user->update([
                'is_active_notification_fcm' => $data['is_active_notification_fcm'],
            ]);
        }

        if (array_key_exists('de_active_community_at', $data)) {
            $result = $user->update([
                'de_active_community_at' => $data['de_active_community_at'] ? now() : null
            ]);
        }

        return $result;
    }
}

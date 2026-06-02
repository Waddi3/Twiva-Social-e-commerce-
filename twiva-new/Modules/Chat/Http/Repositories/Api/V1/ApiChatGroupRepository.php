<?php

namespace Modules\Chat\Http\Repositories\Api\V1;

use Modules\Chat\Entities\ChatGroup;
use Modules\Chat\Entities\Timeline;
use Modules\User\Entities\User;

class ApiChatGroupRepository
{
    public function __construct() {}

    public function getGroupTimeline($filters)
    {
        return Timeline::with(['user'])
            ->where(['chat_group_id' => $filters['chat_group_id']])
            ->withCount(['views', 'comments', 'likes'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate($filters['per_page']);
    }

    public function listOfChatGroups($filters)
    {
        $user = auth('api')->user();
        $groups_query = ChatGroup::query()
            ->withCount('users')
            ->latest();

        if (!$user->is_admin_for_community) {
            $groups_query->whereHas('users', function ($where_has) use ($user) {
                return $where_has->where('user_id', $user->id);
            });
        }

        if (array_key_exists('chat_group_id', $filters) && $filters['chat_group_id']) {
            $groups_query->where('id', $filters['chat_group_id']);
            // $groups_query->with('users');
        }

        return $groups_query->paginate($filters['per_page']);
    }

    public function listOfMembersChatGroups($filters, $group)
    {
        $groups_query = ChatGroup::query()
            ->with('users')
            ->withCount('users')
            ->latest();

        if ($group) {
            $groups_query->where('id', $group);
        }

        $group = $groups_query->first();
        if ($group) {
            return $group->users()->latest('chat_group_users.created_at')->paginate($filters['per_page']);
        }
        return collect();
    }

    public function create($data)
    {

        $data['creator_id'] = auth('api')->id();
        $put_in = ChatGroup::pathImage();
        if (array_key_exists('image', $data)) {
            $data['image'] = uploadFile($data['image'], $put_in);
        }

        $chat_group = ChatGroup::create($data);

        return $chat_group;
    }

    public function update($data, $group_id)
    {

        $put_in = ChatGroup::pathImage();
        if (array_key_exists('image', $data)) {
            $data['image'] = uploadFile($data['image'], $put_in);
        }

        $chat_group = ChatGroup::where('id', $group_id)
            ->where('creator_id', auth('api')->id())
            ->update($data);

        return $chat_group;
    }

    public function delete($group_id)
    {

        $chat_group = ChatGroup::where('id', $group_id)
            ->where('creator_id', auth('api')->id())
            ->delete();
        return $chat_group;
    }

    public function syncMembers($data)
    {

        $chat_group = ChatGroup::find($data['chat_group_id']);
        $result = $chat_group->users()->sync($data['user_ids']);
        return $result;
    }

    public function users($filters)
    {
        $users_query = User::query()
            ->where('type', User::InfluencerType)
            ->whereNull('de_active_community_at')
            ->whereNull('banned_at');

        if (array_key_exists('query', $filters) && $filters['query']) {
            $users_query->where('name', 'like', '%' . $filters['query'] . '%');
        }
        if ($filters['chat_group_id'] ?? false) {
            $users_query->whereHas('chatGroups', function ($chatGroups) use ($filters) {
                $chatGroups->where('chat_group_id', $filters['chat_group_id']);
            });
        }

        return $users_query->paginate($filters['per_page']);
    }

    public function usersBanned($filters)
    {
        $users_query = User::query()
            ->select(['id', 'name', 'profile_image', 'banned_at'])
            ->where('type', User::InfluencerType)
            ->whereNotNull('banned_at');

        if (array_key_exists('query', $filters) && $filters['query']) {
            $users_query->where('name', 'like', '%' . $filters['query'] . '%');
        }

        return $users_query->paginate($filters['per_page']);
    }

    public function userBanned($user)
    {
        $auth_user_id = auth('api')->id();
        $user = User::query()
            ->where('type', User::InfluencerType)
            ->where('id', $user)
            ->first();

        $result = false;
        if ($auth_user_id != $user->id) {
            $banned_at = is_null($user->banned_at) ? now() : null;

            $result = $user->update([
                'banned_at' => $banned_at,
            ]);
        }
        return $result;
    }
}

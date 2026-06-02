<?php

namespace Modules\User\Repository\Api;

use Modules\User\Entities\Badge;
use Modules\User\Entities\BadgeUser;

class ApiBadgeRepository
{

    public function list($filters)
    {
        $badges_query = Badge::query()
            ->with(['users' => function ($with_count) {
                return $with_count->select('users.id', 'name', 'phone_number', 'phone_code', 'email')
                    ->where('badge_users.user_id', auth('api')->id())->latest('badge_users.created_at');
            }])
            ->withCount(['users' => function ($with_count) {
                return $with_count
                    ->where('badge_users.user_id', auth('api')->id());
            }])
            ->orderByDesc('users_count');

        // dd($badges_query->get());
        return $badges_query->paginate($filters['per_page']);
    }

    public function update($data)
    {
        $user = auth('api')->user();

        $badges = $user->badges()
            ->latest('badge_users.created_at')
            ->whereIn('badge_id', $data['badge_ids'])
            ->get();
        // dd($data['badge_ids'], $badges->where('id', $data['badge_ids'][0]));

        $badge_ids = collect($data['badge_ids']);

        $prepare_badges_relations = $badge_ids->map(function ($value, $key) use ($badges, $user) {
            $status = $badges->where('id', $value)->first()?->pivot?->status;
            if ($status == BadgeUser::STATUSES['declined'] || is_null($status)) {
                return [
                    'user_id' => $user->id,
                    'badge_id' => $value,
                    'status' => BadgeUser::STATUSES['pending'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        })->filter(function ($item) {
            return $item;
        })->values()->toArray();

        $result = BadgeUser::insert($prepare_badges_relations);
        return $result;
    }
}

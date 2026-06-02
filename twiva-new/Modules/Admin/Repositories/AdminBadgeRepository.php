<?php

namespace Modules\Admin\Repositories;

use Modules\User\Entities\BadgeUser;

class AdminBadgeRepository
{
    public function __construct() {}

    public function resellerRequested($filters)
    {
        /** filters:
         * page
         * per_page
         * badge_user_id
         * query
         */

        $requested_query = BadgeUser::query()
            ->with('user:id,name,phone_number,phone_code,email', 'badge')
            ->latest();


        if (array_key_exists('badge_user_id', $filters) && $filters['badge_user_id']) {
            $badge_user_id = $filters['badge_user_id'];
            $requested_query->where('id', $badge_user_id);
        }

        if (array_key_exists('query', $filters) && $filters['query']) {
            $query = $filters['query'];
            $requested_query->where(function ($where) use ($query) {
                return $where->where('status', $query)
                    ->orWhereHas('user', function ($where_has) use ($query) {
                        return $where_has->where('name', 'like', "%$query%")
                            ->orWhere('phone_number', 'like', "%$query%")
                            ->orWhere('email', 'like', "%$query%");
                    })->orWhereHas('badge', function ($or_where_has) use ($query) {
                        return $or_where_has->where('title', 'like', "%$query%");
                    });
            });
        }
        return $requested_query->paginate($filters['per_page']);
    }

    public function update($data)
    {
        return BadgeUser::where('id', $data['id'])->update([
            'status' => $data['status'],
            'comments' => $data['comments'],
        ]);
    }
}

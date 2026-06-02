<?php


namespace Modules\Platform\Repositories;

use Modules\Platform\Entities\WhatsAppGroup;

class WhatsAppGroupRepository
{
    public function listOfWhatsAppGroup($filter)
    {
        $whats_app_groups_query = WhatsAppGroup::query()
            ->latest('id')
            ->verified();
        if ($filter) {
            if (array_key_exists('query', $filter) && $filter['query']) {
                $query = $filter['query'];
                $whats_app_groups_query->where(function ($where) use ($query) {
                    return $where->where('name', 'like', "%$query%")
                        ->orWhere('number_of_people', $query)
                        ->orWhere('city_id', $query)
                        ->orWhereHas('city', function ($or_where_has) use ($query) {
                            return $or_where_has->where('name', 'like', "%$query%");
                        })->orWhereHas('user', function ($or_where_has) use ($query) {
                            return $or_where_has->where('name', 'like', "%$query%");
                        })->orWhereHas('CategoryWhatsAppGroups.categorizable', function ($or_where_has) use ($query) {
                            return $or_where_has->where('name', 'like', "%$query%");
                        });
                });
            }
            if (array_key_exists('categories', $filter) && $filter['categories']) {
                $categories = $filter['categories'];
                $whats_app_groups_query->where(function ($where) use ($categories) {
                    return $where->whereHas('CategoryWhatsAppGroups.categorizable', function ($where_has) use ($categories) {
                        return $where_has->whereIn('id',  $categories);
                    });
                });
            }
            if (array_key_exists('sort_by', $filter) && $filter['sort_by']) {
                $sort_by = $filter['sort_by'];
                $sort_direction = $filter['sort_direction'];
                $whats_app_groups_query->orderBy($sort_by, $sort_direction);
            }
            if (array_key_exists('city_id', $filter) && $filter['city_id']) {
                $city_id = $filter['city_id'];
                $whats_app_groups_query->where('city_id', $city_id);
            }
        }

        return $whats_app_groups_query->paginate();
    }
}

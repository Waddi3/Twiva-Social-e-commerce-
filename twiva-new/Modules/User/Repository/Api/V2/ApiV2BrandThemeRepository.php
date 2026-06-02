<?php


namespace Modules\User\Repository\Api\V2;

use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Interest;
use Modules\User\Entities\Language;

class ApiV2BrandThemeRepository
{
    public function index()
    {
        $user = auth('api')->user();
        $bran_theme = $user->brandTheme()
            ->select('*')
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', languages.id, 'name', languages.name)) FROM languages WHERE FIND_IN_SET(languages.id, brand_themes.language_ids)) AS languages")
            )
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', interests.id, 'name', interests.name)) FROM interests WHERE FIND_IN_SET(interests.id, brand_themes.interest_ids)) AS interests")
            )
            ->first();
        return $bran_theme;
    }

    public function update($data)
    {
        if (array_key_exists('language_ids', $data)) {
            $data['language_ids'] = collect($data['language_ids'])->implode(',');
        }
        if (array_key_exists('interest_ids', $data)) {
            $data['interest_ids'] = collect($data['interest_ids'])->implode(',');
        }


        if ($data['interest_list'] ?? false) {
            $interests_name = [];
            foreach ($data['interest_list'] as $interest) {
                $interests_name[] = ['name' => $interest];
            }
            Interest::query()->upsert($interests_name, ['name']);
            $data['interest_ids'] = Interest::query()
                ->select('id')
                ->orderBy('id')
                ->whereIn('name', $data['interest_list'])
                ->pluck('id')
                ->implode(',');
        }

        $user = auth('api')->user();

        $result = $user->brandTheme()->updateOrCreate([], $data);
        return $result;
    }

    public function languages()
    {
        return Language::select(['id', 'name'])->get();
    }
    public function interests()
    {
        return Interest::select(['id', 'name'])->get();
    }
}

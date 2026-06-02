<?php


namespace Modules\Influencer\Repositories;


use Carbon\Carbon;
use Modules\Shop\Entities\Shops;
use Modules\Platform\Entities\UserPlatform;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;

class InfluencerRepository
{
    public function searchInInfluencers($influencers)
    {
        if (request('location')) {
            $influencers = $influencers->whereHas('InfluencerDetails', function ($query) {
                return $query->where('location', 'LIKE', '%' . request('location') . '%');
            });
        }
        if (request('content_type')) {
            $influencers = $influencers->whereHas('contentTypes', function ($query) {
                $query->select('influencer_id')
                    ->selectRaw('count(content_type_id) as content_type_id_count')
                    ->whereIn('content_type_id', request('content_type'))
                    ->groupBy('influencer_id')
                    ->having('content_type_id_count', '=', count(request('content_type')));
            });
        }
        if (request('gender')) {
            $influencers = $influencers->whereHas('InfluencerDetails', function ($query) {
                return $query->whereIn('gender', request('gender'));
            });
        }
        if (request('age_range')) {
            $influencers = $influencers->whereHas('InfluencerDetails', function ($query) {
                return $query->whereRaw('timestampdiff(year, dob, curdate()) between ? and ?', explode('-', request('age_range')));
            });
        }
        if (request('influencer_name')) {
            $influencers = $influencers
//                ->whereHas('InfluencerDetails', function ($query) {
//                return $query
                ->where(function ($where) {
                    $where->where('name', 'LIKE', '%' . request('influencer_name') . '%')
                        ->orWhere('email', 'LIKE', '%' . request('influencer_name') . '%');
                });
//            });
//            $influencers->dd();
        }
        if (request('tiers')) {
            if (!in_array('Any', request('tiers'))) {
                $influencers = $influencers->whereHas('UserPlatforms', function ($query) {
                    $query->select('user_id')
                        ->selectRaw('sum(followers) as followers_sum')
                        ->groupBy('user_id');
                    foreach (request('tiers') as $tire) {
                        $query->orHavingRaw('followers_sum Between ? and ?', UserPlatform::Tires[$tire]);
                    }
                    return $query;
                });
            }
        }
        if (request('social_categories')) {
            $influencers = $influencers->whereHas('influencersSocialCategories', function ($record) {
                return $record->whereIn('id', request('social_categories'));
            });
        }
        if (request('interests')) {
            foreach (request('interests') as $interest) {
                $influencers = $influencers->whereHas('InfluencerDetails', function ($query) use ($interest) {
                    return $query->where('interests', 'LIKE', '%' . $interest . '%');
                });
            }

        }
        if (request('engagements_range')) {
            $range = request('engagements_range');
            if (!((int)$range[0] == 0 && (int)$range[1] == 100000)) {
                $influencers = $influencers->whereHas('UserPlatforms', function ($record) use ($range) {
                    $min = $range[0] * 1000;
                    $max = $range[1] * 1000;
                    $record->whereBetween('followers', [$min, $max]);
                });
            }

        }
        if (request('platforms')) {
            $platforms = request('platforms');
            $influencers = $influencers->whereHas('UserPlatforms', function ($record) use ($influencers, $platforms) {
                $record->select('user_id', 'platform_id', 'followers', 'engagements', 'avg_engagements_per_post')
                    ->whereIn('platform_id', $platforms);
            });
        }
        return $influencers;
    }

    public function getInfluencers()
    {
        return
            User::select('id', 'name', 'type', 'profile_image', 'description')->where(
                [
                    'type' => 'Influencer',
                ])
                ->with(
                    ['UserPlatforms' => function ($query) {
                        return $query->select('user_id', 'platform_id', 'social_id', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'additional_info');
                    }],
                    ['InfluencerDetails' => function ($query) {
                        return $query->select('id', 'influencer_id', 'location')->whereNotNull('influencer_detail_new.description');
                    }])
                    ->whereHas('UserPlatforms');
    }

    public function orderInfluencerBy($influencers)
    {
        if (!$influencers) {
            $influencers = User::where('type', 'Influencer')
                ->with('Interests', 'InfluencerDetails', 'socialEngagements')
                ->leftJoin('social_engagements', 'users.id', '=', 'social_engagements.influencer_id');
        }
        $influencers = $influencers->leftJoin('social_engagements', 'users.id', '=', 'social_engagements.influencer_id');

        switch (request('order_by')) {

            case'highest_followers':
                $influencers = $influencers
                    ->orderBy('social_engagements.followers', 'DESC');
                break;
            case'lowest_followers':
                $influencers = $influencers
                    ->orderBy('social_engagements.followers', 'ASC');
                break;
            case'highest_engagements':
                $influencers = $influencers
                    ->orderBy('social_engagements.engagements', 'DESC');
                break;
            case'lowest_engagements':
                $influencers = $influencers
                    ->orderBy('social_engagements.engagements', 'ASC');
                break;
            case'highest_avg_engagements':
                $influencers = $influencers
                    ->orderBy('social_engagements.avg_engagements_per_post', 'DESC');
                break;
            case'lowest_avg_engagements':
                $influencers = $influencers
                    ->orderBy('social_engagements.avg_engagements_per_post', 'ASC');
                break;
        }
        return $influencers ?? null;
    }

    public static function updateInfluencer($influencer, $data)
    {
        $data['dob'] = Carbon::parse($data['dob'])->format('Y-m-d');
        $data['interests'] = implode(',', $data['interests']);
        if (isset($data['profile_image'])) {
            $data['profile_image'] = $data['profile_image']->store(User::IMAGE_PATH, 'public');
            $data['profile_image'] = str_replace(User::IMAGE_PATH, '', $data['profile_image']);
        }
        $influencerDetailsData = [];
        $influencerDetailsAttributes = array_keys($influencer->InfluencerDetails->getAttributes());

        foreach ($influencerDetailsAttributes as $influencerDetailsAttribute) {
            if (isset($data[$influencerDetailsAttribute])) {
                $influencerDetailsData[$influencerDetailsAttribute] = $data[$influencerDetailsAttribute];
            }
        }
        $userAttributes = array_keys($influencer->getAttributes());
        $userData = [];
        foreach ($userAttributes as $userAttributeAttribute) {
            if (isset($data[$userAttributeAttribute])) {
                $userData[$userAttributeAttribute] = $data[$userAttributeAttribute];
            }
        }
        if (isset($data['content_type'])) {
            $influencer->contentTypes()->sync($data['content_type']);
            unset($data['content_type']);
        }

        if ($influencer->phone_number != $data['phone_number']) {
            $userData['mobile_verified_at'] = null;
        }
        $influencer->update($userData);
        $influencer->influencerDetails()->update($influencerDetailsData);

    }

    public static function createInfluencer($data)
    {
        $data['type'] = User::InfluencerType;
        $data['interests'] = implode(',', $data['interests'] ?? []);
        $data['password'] = bcrypt($data['password']);
        $data['referral_code'] = UserRepository::createReferralCode();
        if (isset($data['profile_image'])) {
            $data['profile_image'] = $data['profile_image']->store(User::IMAGE_PATH, 'public');
            $data['profile_image'] = str_replace(User::IMAGE_PATH, '', $data['profile_image']);
        }
        $influencerDetailsData['interests'] = $data['interests'];
        $influencerDetailsData['content_type'] = $data['content_type'] ?? [];
        $influencerDetailsData['location'] = $data['location'];
        $influencerDetailsData['dob'] = $data['dob'];
        $influencerDetailsData['gender'] = $data['gender'];
        unset($data['interests']);
        unset($data['content_type']);
        unset($data['location']);
        unset($data['dob']);
        unset($data['gender']);

        $influencer = User::create($data);
        $influencer->contentTypes()->sync($influencerDetailsData['content_type']);

        unset($influencerDetailsData['content_type']);
        $influencer->InfluencerDetails()->create($influencerDetailsData);
        return $influencer;
    }


}

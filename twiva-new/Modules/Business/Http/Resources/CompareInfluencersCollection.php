<?php

namespace Modules\Business\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompareInfluencersCollection extends JsonResource
{

    public function __construct($influencers)
    {
        parent::__construct($influencers);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description ?? 'N/A',
            'photo_path' => $this->profile_image ?? 'N/A',
            'location' => isset($this->InfluencerDetails) ? $this->InfluencerDetails->location : 'N/A',
            'interests' => $this->interests,
//            'categories' => $this->influencersSocialCategories->map(function ($categoriey) {
//                return collect($categoriey->toArray())->only('id', 'name')->all();
//            }),
            'platforms' => $this->UserPlatforms->map(function ($platform) {
                $platform->social_link =  true;
                return collect($platform->toArray())->only(['platform_id','social_link', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'updated_at'])
                    ->all();
            }),
        ];
    }
}

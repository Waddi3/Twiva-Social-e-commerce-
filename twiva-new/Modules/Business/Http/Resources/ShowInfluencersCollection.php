<?php

namespace Modules\Business\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowInfluencersCollection extends ResourceCollection
{
    private $influencer;

    public function __construct($influencer)
    {
        $this->influencer = $influencer;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->influencer->id,
            'name' => $this->influencer->name,
            'description' => $this->influencer->description ?? 'N/A',
            'photo_path' => $this->influencer->profile_image ?? 'N/A',
            'location' => $this->influencer->InfluencerDetails->location ?? 'N/A',
            'interests' => $this->influencer->interests,
            'platforms' => $this->influencer->UserPlatforms->map(function ($platform) {
                $platform->social_link = $platform->platform->link;
                return collect($platform->toArray())->only(['platform_id', 'social_link', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'updated_at'])
                    ->all();
            }),
        ];
    }
}

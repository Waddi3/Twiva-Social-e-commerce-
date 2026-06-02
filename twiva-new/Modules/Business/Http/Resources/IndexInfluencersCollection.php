<?php

namespace Modules\Business\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexInfluencersCollection extends JsonResource
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
            'platforms' => $this->UserPlatforms->map(function ($platform) {
                $platform->social_link = $platform->platform->link;
                return collect($platform->toArray())->only(['social_link', 'platform_id', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'updated_at'])
                    ->all();
            }),
        ];
    }
}

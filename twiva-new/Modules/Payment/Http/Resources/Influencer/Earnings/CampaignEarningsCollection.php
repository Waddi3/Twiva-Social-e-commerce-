<?php

namespace Modules\Payment\Http\Resources\Influencer\Earnings;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignEarningsCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_type' => $this->user_type,
            'title' => $this->post->post_name,
            'business_name' => $this->post->user->name ?? $this->post->id,
            'main_image' => $this->post->mainImage,
            'amount' => $this->influencerAmount,
        ];
    }
}

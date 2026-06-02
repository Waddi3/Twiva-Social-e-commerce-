<?php

namespace Modules\Campaign\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexBroadcastInvitationsCollection extends JsonResource
{
    public function __construct($invations)
    {
        parent::__construct($invations);
    }

    public function toArray($request)
    {
        $influencer_post = $this->influencersInPost()->where('user_id', auth('api')->id())->first();
        return [
            'id' => $influencer_post?->id,
            'title' => $this->post_name,
            'post_id' => $this->id,
            'original_price' => (int)$this->price_per_post,
            'final_price' => $influencer_post?->influencerPriceObject()->final_price,
            'image' => $this->mainImage,
            'business_id' => $this->user_id,
            'post_type' => $this->post_type,
            'expiration_date' => $this->expiration_date,
            'status' => $influencer_post?->statusStringForBusiness ?? 'Pending',
            'post_product_count' => $this->products_count,
//            'influencersInPost' => ShowInvitationResource::collection($this->influencersInPost()->take(10)->get())->resource,
        ];
    }
}

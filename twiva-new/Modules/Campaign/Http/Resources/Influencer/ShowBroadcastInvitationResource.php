<?php

namespace Modules\Campaign\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowBroadcastInvitationResource extends ResourceCollection
{
    private $invation;

    public function __construct($invation)
    {
        $this->invation = $invation;
    }

    public function toArray($request)
    {
        $influencer_post = $this->invation->influencersInPost()->where('user_id', auth('api')->id())->first();
        return [
            'id' => $influencer_post?->id,
            'post_id' => $this->invation->id,
            'post_name' => $this->invation->post_name,
//            'title' => $this->invation->post_name,
            'post_type' => $this->invation->post_type,
            'post_images' => $this->invation->Images->map(function ($image) {
                $image->image = $image->mediaPath;
                return collect($image)->only('image');
            }),
            'expiration_date' => $this->invation->expiration_date ?? null,
            'description' => $this->invation->description,
            'hashtag' => $this->invation->hashtag,
            'status' => $influencer_post?->statusStringForBusiness ?? 'Pending',
            'influncer_price' => (float)$influencer_post?->reject_amount ?? null,
            'business_price' => (float)$this->invation->price_per_post,
            'final_price' => $influencer_post?->final_price,
//            'business_id' => $this->invation->user_id,
            'application' => $influencer_post?->reason,
            'post_products' => $this->invation->products,
        ];
    }

}

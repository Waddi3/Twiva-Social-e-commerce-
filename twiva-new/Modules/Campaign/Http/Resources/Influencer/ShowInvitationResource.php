<?php

namespace Modules\Campaign\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\Platform\Http\Resources\V2\WhatsAppGroupPlatformCollection;

class ShowInvitationResource extends ResourceCollection
{
    private $invation;

    public function __construct($invation)
    {
        $this->invation = $invation;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->invation->id,
            'post_id' => $this->invation->post->id,
            'post_name' => $this->invation->post->post_name,
            'post_images' => $this->invation->post->Images->map(function ($image) {
                $image->image = $image->mediaPath;
                return collect($image)->only('image');
            }),
            'expiration_date' => $this->invation->post->expiration_date ?? null,
            'description' => $this->invation->post->description,
            'hashtag' => $this->invation->post->hashtag,
            'status' => $this->invation->statusStringForBusiness,
            'influncer_price' => (float)$this->invation->reject_amount ?? null,
            'business_price' => (float)$this->invation->businessInfluencerPrice ?? null,
            'final_price' => (float)$this->invation->influencerPriceObject()?->final_price ?? null,
            'application' => $this->invation->reason,
            'post_products' => $this->invation->Post->products,
            'whatsapp_group' => $this->when($this->invation->user_type == (new WhatsAppGroup())->getMorphClass(), new WhatsAppGroupPlatformCollection($this->invation->whatsAppGroup)),
        ];
    }
}

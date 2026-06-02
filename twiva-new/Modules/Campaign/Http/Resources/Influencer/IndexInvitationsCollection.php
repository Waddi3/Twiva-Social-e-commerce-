<?php

namespace Modules\Campaign\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Campaign\Entities\PostUser;
use Modules\Platform\Entities\WhatsAppGroup;

/** @mixin PostUser */
class IndexInvitationsCollection extends JsonResource
{
    public function __construct($invations)
    {
        parent::__construct($invations);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->post->post_name,
            'post_id' => $this->post->id,
            'original_price' => (int)$this->post->price_per_post,
            'businessInfluencerPrice' => (int)$this->business_influencer_price,
            'final_price' => $this?->influencerPriceObject()?->final_price,
            'image' => $this->post->main_image,
            'status' => $this->status_string_for_business,
            'business_id' => $this->post->user_id,
            'post_product_count' => $this->post->products_count,
            'user_type' => $this->user_type,
            'whats_app_group_name' => $this->user_type == ((new WhatsAppGroup())->getMorphClass()) ? $this->whatsAppGroup->name : null,
        ];
    }
}

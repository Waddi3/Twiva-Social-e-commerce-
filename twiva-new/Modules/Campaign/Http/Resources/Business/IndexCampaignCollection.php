<?php

namespace Modules\Campaign\Http\Resources\Business;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexCampaignCollection extends JsonResource
{
    public function __construct($campaigns)
    {
        parent::__construct($campaigns);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_name' => $this->post_name,
            'user_type' => $this->user_type,
            'post_image' => $this->Images->isNotEmpty() ? asset($this->Images[0]->mediaPath) : null,
            'expiration_date' => $this->expiration_date ?? null,
            'applied' => $this->appliedCount,
            'pending' => $this->pendingCount,
            'declined' => $this->declinedCount,
            'hired' => $this->hiredCount,
            'influencers_count' => $this->appliedCount + $this->pendingCount + $this->declinedCount + $this->hiredCount,
            'status' => 'OnGoing',
            'approve_status' => $this->approve_status,
            'post_type' => $this->post_type,
            'post_product_count' => $this->products_count,
        ];
    }
}

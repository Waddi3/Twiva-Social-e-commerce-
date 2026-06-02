<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerOrderShowDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'product_id' => 43921
            'quantity' => $this->quantity,
            'size' => $this->sizeDetailes?->name,
            'color' => $this->colorDetailes?->name,
            'price' => $this->price,
            'name' => $this->name,
            'image' => $this->image,
        ];
    }
}

<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerOrderShowResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->order_amount,
            'shipping_amount' => $this->shipping_amount,
            'order_date' => $this->created_at->format('M d Y'),
            'status' => $this->status_string,
            'tentative_period_text' => $this->tentative_period_text(),
            'items' => $this->qty,
            'products_count' => $this->order_list_count,
            'order_details' => ApiBuyerOrderShowDetailsResource::collection($this->orderList),
        ];
    }
}

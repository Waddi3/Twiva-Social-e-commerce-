<?php

namespace Modules\Partner\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerOrdersResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->order_amount + $this->shipping_amount,
            'status' => $this->statusString,
            'customer' => $this->buyer ? $this->buyer->name : "",
            'business' => $this->business ? $this->business->name : "",
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}

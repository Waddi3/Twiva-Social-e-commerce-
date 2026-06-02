<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiBuyerProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_verified' => $this->is_verified,
            'phone_code' => $this->phone_code,
            'phone_number' => $this->phone_number,
            'mobile' => $this->mobile,
            'class' => $this->class,
            'rep_name' => $this->rep_name,
            'retail_store_name' => $this->retail_store_name,
            'rep_phone_code' => $this->rep_phone_code,
            'rep_phone_number' => $this->rep_phone_number,
            'rep_mobile' => $this->rep_mobile,
            // 'addresses' => ApiBuyerProfileAddressResource::collection($this->addresses),
        ];
    }
}

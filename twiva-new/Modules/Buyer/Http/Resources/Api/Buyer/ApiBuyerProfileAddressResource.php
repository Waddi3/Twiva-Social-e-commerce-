<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiBuyerProfileAddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'delivery_address' => $this->delivery_address,
            'city_id' => $this->city_id,
            'city' => $this->city->name,
            'country_id' => $this->country_id,
            'country' => $this->country->name,
            'county_id' => $this->county_id,
            'county' => $this->county->name,
            'phone_number' => $this->phone_number,
            // 'mobile' => $this->mobile,
            'email' => $this->email,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}

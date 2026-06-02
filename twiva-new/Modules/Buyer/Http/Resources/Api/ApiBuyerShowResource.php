<?php

namespace Modules\Buyer\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Buyer\Entities\Buyer;

class ApiBuyerShowResource extends JsonResource
{
    public function toArray($request)
    {
//        $name = Buyer::splitName($this->name);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "is_verified" => $this->is_verified,
            "phone_code" => $this->phone_code,
            "phone_number" => $this->phone_number,
            "class" => $this->class,
            "rep_name" => $this->rep_name,
            "retail_store_name" => $this->retail_store_name,
            "rep_phone_code" => $this->rep_phone_code,
            "rep_phone_number" => $this->rep_phone_number,
            "deleted_at" => $this->deleted_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => $this->created_by,
            "addresses" => $this->getAddresses(),
        ];
    }

    private function getAddresses()
    {
        $addresses = collect();
        foreach ($this->addresses as $address) {
            $address->latitude = (string)$address->latitude;
            $address->longitude = (string)$address->longitude;
            $addresses->push($address);
        }
        return $addresses;
    }
}

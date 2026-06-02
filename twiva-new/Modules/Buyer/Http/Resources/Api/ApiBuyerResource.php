<?php

namespace Modules\Buyer\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Buyer\Entities\Buyer;

class ApiBuyerResource extends JsonResource
{
    public function toArray($request)
    {
        $name = Buyer::splitName($this->name);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "mobile" => $this->mobile,
            "created_at" => $this->created_at?->format('d M Y'),
            "is_verified" => $this->is_verified,
//            "first_name" => $name[0] ?? "",
//            "last_name" => $name[1] ?? "",
//            "email" => $this->email,
//            "password" => $this->password,
//            "phone_code" => $this->phone_code,
//            "phone_number" => $this->phone_number,
//            "class" => $this->class,
//            "rep_name" => $this->rep_name,
//            "retail_store_name" => $this->retail_store_name,
//            "rep_phone_code" => $this->rep_phone_code,
//            "rep_phone_number" => $this->rep_phone_number,
//            "deleted_at" => $this->deleted_at,
//            "updated_at" => $this->updated_at,
//            "created_by" => $this->created_by,
        ];
    }
}

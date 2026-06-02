<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerRegisterResource extends JsonResource
{
    public $token;
    public function __construct($resource, $token)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->token = $token;
    }

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "is_verified" => $this->is_verified,
            "phone_code" => $this->phone_code,
            "phone_number" => $this->phone_number,
            "mobile" => $this->mobile,
            "class" => $this->class,
            "rep_name" => $this->rep_name,
            "retail_store_name" => $this->retail_store_name,
            "rep_phone_code" => $this->rep_phone_code,
            "rep_phone_number" => $this->rep_phone_number,
            "auth_code" => $this->auth_code,
            // "deleted_at" => $this->deleted_at,
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
            // "created_by" => $this->created_by,
            'token' => $this->token,
        ];
    }
}

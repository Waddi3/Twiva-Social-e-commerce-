<?php

namespace Modules\Home\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiCityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "state_id" => $this->state_id,
            "state_code" => $this->state_code,
            "country_id" => $this->country_id,
            "country_code" => $this->country_code,
            "latitude" => (float)$this->latitude ?? 0,
            "longitude" => (float)$this->longitude ?? 0,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "flag" => $this->flag,
            "wikiDataId" => $this->wikiDataId,
        ];
    }
}

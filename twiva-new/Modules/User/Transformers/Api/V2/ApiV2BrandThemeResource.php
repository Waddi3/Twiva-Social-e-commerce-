<?php

namespace Modules\User\Transformers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiV2BrandThemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "voice" => $this->voice,
            "tone" => $this->tone,
            "style" => $this->style,
            "structure" => $this->structure,
            "location" => $this->location,
            "city_id" => $this->city_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            "min_age" => $this->min_age,
            "max_age" => $this->max_age,
            "gender" => $this->gender,
            // "language_ids" => $this->language_ids,
            // "interest_ids" => $this->interest_ids,
            "languages" => json_decode($this->languages),
            "interests" => json_decode($this->interests),
        ];
    }
}

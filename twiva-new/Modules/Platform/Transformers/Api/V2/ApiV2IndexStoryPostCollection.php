<?php

namespace Modules\Platform\Transformers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiV2IndexStoryPostCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => $this->id,
            'productDetails' => $this->productDetails,
            'platforms' => json_decode($this->platforms),
            'is_schedule' => $this->is_schedule,
            'canceled_at' => $this->canceled_at,
            'schedule_post' => $this->start_date_format,
            'posted_on' => $this->posted_on,
            'end_date' => $this->end_date,
            'updated_at' => $this->updated_at,
            'is_completed' => $this->is_completed,
        ];
    }
}

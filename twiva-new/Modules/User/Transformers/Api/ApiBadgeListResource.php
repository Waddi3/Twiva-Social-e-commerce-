<?php

namespace Modules\User\Transformers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiBadgeListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'background_color' => $this->background_color,
            'color' => $this->color,
            'has_badge' => (bool)$this->users_count,
            'status' => $this->users?->first()?->pivot?->status,
            'comments' => $this->users?->first()?->pivot?->comments,
        ];
    }
}

<?php

namespace Modules\Chat\Transformers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiChatGroupUserLisResource extends JsonResource
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
            "name" => $this->name,
            "profile" => $this->profile_image,
            "in_group" => $this->chat_groups_count,
            "banned_at" => $this->banned_at,
        ];
    }
}

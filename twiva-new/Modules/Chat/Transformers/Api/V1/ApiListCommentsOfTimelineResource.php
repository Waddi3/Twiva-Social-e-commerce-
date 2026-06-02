<?php

namespace Modules\Chat\Transformers\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Chat\Entities\Comment;

/** @mixin Comment */
class ApiListCommentsOfTimelineResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "created_at" => $this->added_at,
            "user" => ApiUserTimeLineResource::collection($this->user()->get()),
            'reply_count' => $this->comments_count,
        ];
    }
}

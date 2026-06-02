<?php

namespace Modules\Chat\Transformers\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Chat\Entities\Timeline;

/** @mixin Timeline*/
class ApiGroupTimelineListResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "image" => $this->image_url,
            "is_pinned" => (bool)$this->is_pinned,
            "chat_group_id" => $this->chat_group_id,
            "created_at" => $this->added_at,
            "user_id" => $this->user_id,
            "views_count" => $this->views_count,
            "likes_count" => $this->likes_count,
            "comments_count" => $this->comments_count,
            "user" => ApiUserTimeLineResource::collection($this->user()->get()),
        ];
    }
}

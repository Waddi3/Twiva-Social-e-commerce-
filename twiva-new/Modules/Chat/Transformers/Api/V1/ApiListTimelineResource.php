<?php

namespace Modules\Chat\Transformers\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Chat\Entities\Timeline;

/**@mixin Timeline */
class ApiListTimelineResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "image" => $this->image_url,
            "is_pinned" => (bool)$this->is_pinned,
            "chat_group_id" => null,
            "created_at" => $this->added_at,
            "user_id" => $this->user_id,
            "views_count" => $this->views_count,
            "likes_count" => $this->likes_count,
            'isLiked' => $this->likes->pluck('user_id')->contains(auth()?->id() ?? 0),
            "comments_count" => $this->comments_count,
            "user" => ApiUserTimeLineResource::collection($this->user()->get()),
            "deleted_at" => $this->deleted_at,
        ];
    }
}

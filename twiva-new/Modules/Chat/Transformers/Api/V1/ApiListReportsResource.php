<?php

namespace Modules\Chat\Transformers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiListReportsResource extends JsonResource
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
            "status" => $this->status,
            "content" => $this->content,
            "user_id" => $this->user_id,
            "user" => ApiUserTimeLineResource::collection($this->user()->get()),
            "timeline_id" => $this->timeline_id,
            "timeline" => ApiListTimelineResource::collection($this->timeline()->withTrashed()->withCount('views', 'likes', 'comments')->get()),
            "report_reason_id" => $this->report_reason_id,
            "report_reason" => $this->reportReason->only('id', 'reason'),
            "created_at" => $this->addedAt,
        ];
    }
}

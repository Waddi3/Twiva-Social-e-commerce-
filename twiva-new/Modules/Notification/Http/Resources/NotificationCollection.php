<?php

namespace Modules\Notification\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                'id' => $this->id,
                'title' => $this->data['title'] ?? null,
                'flutter_action' => $this->data['flutter_action'] ?? null,
                'reason_id' => $this->data['reason_id'] ?? null,
                'created_at' => $this->created_at,

            ];
    }
}

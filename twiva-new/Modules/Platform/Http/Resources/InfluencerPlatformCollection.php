<?php

namespace Modules\Platform\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InfluencerPlatformCollection extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'platform' => $this->platformName->only('id', 'name'),
            'status' => $this->status,
        ];
    }
}

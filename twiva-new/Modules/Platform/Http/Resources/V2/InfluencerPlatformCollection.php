<?php

namespace Modules\Platform\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Platform\Entities\Platform;

class InfluencerPlatformCollection extends JsonResource
{
    private $userPlatforms;

    public function __construct($resource)
    {
        $this->userPlatforms = auth('api')->user()->UserPlatforms()
            ->select(['id', 'platform_id', 'token', 'user_id', 'additional_info'])
            // ->whereNotNull('token')
            ->get();
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'platform' => (object)[
                'id' => $this->id,
                'name' => $this->name
            ],
            'is_connected' => $this->userPlatforms->where('platform_id', $this->id)->first()?->status,
            'canPost' => in_array($this->id, Platform::AllowedToPostPlatform),
            'username' => $this->userPlatforms->where('platform_id', $this->id)->first()?->username
        ];
    }
}

<?php

namespace Modules\Platform\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\WhatsAppGroup;

class WhatsAppGroupPlatformCollection extends JsonResource
{
    private $userPlatforms;

    // public function __construct($resource)
    // {
    //     $this->userPlatforms = auth('api')->user()->UserPlatforms()
    //         ->select(['id', 'platform_id', 'token'])
    //         ->whereNotNull('token')
    //         ->get();
    //     parent::__construct($resource);
    // }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_of_people' => $this->number_of_people,
            'city_id' => $this->city_id,
            'city' => "{$this->city?->name}, {$this->city?->country->name}",
            'invite_link' => $this->invite_link,
            'status' => $this->status,
            'categories' => WhatsAppGroupPlatformCategoriesCollection::collection($this?->CategoryWhatsAppGroups),
            'reason_rejections' => $this->status == WhatsAppGroup::STATUSES['rejected'] ? $this?->reasonRejections->pluck('reason') : [],
        ];
    }
}

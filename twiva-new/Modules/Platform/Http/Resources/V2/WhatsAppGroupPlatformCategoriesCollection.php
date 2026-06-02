<?php

namespace Modules\Platform\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\WhatsAppGroup;

class WhatsAppGroupPlatformCategoriesCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->categorizable->id,
            'name' => $this->categorizable->name,
            'categorizable_id' => $this->categorizable_id,
            'categorizable_type' => $this->categorizable_type,
        ];
    }
}

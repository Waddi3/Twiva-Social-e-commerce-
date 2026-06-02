<?php

namespace Modules\System\Http\Resources\MobileVersion;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MobileVersionCollection extends ResourceCollection
{

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function toArray($request)
    {
        return [
            'platform' => $this->version->platform,
            'version' => $this->version->version,
            'is_forced' => $this->version->is_forced,
            'store_link' => $this->version->store_link,
        ];
    }
}

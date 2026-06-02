<?php

namespace Modules\System\Http\Controllers\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\System\Entities\MobileVersion;
use Modules\System\Entities\System;
use Modules\System\Http\Requests\MobileVersion\StoreMobileVersionRequest;
use Modules\System\Http\Requests\MobileVersion\UpdateMobileVersionRequest;
use Modules\System\Http\Requests\UpdateSystemRequest;
use Modules\System\Http\Resources\MobileVersion\MobileVersionCollection;

class ApiMobileVersionController extends Controller
{
    public function check()
    {
        $platform = request('platform');
        if (!$platform) {
            return response()->json(['data' => ['version' => 'Please select the platform']]);
        }
        $version = MobileVersion::where('platform', strtoupper($platform))
            ->latest('version')
            ->first();


        return response()->json(['data' => new MobileVersionCollection($version)]);
    }
}

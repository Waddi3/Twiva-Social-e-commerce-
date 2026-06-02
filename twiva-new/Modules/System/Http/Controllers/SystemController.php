<?php

namespace Modules\System\Http\Controllers;

use Artisan;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Notification\Entities\NotificationTitle;
use Modules\System\Entities\ConstantSystem;
use Modules\System\Entities\MobileVersion;
use Modules\System\Entities\System;
use Modules\System\Http\Requests\UpdateSystemRequest;

class SystemController extends Controller
{
    public function show()
    {
        $versions = MobileVersion::where(function ($where) {
            $where->where('app_name', MobileVersion::ANDROID_PLATFORM)
                ->orWhere('app_name', MobileVersion::IOS_PLATFORM);
        })
            ->get();

        return view('system::manage', [
            'system' => System::first(),
            'versions' => $versions,
            'notifications_list_titles' => NotificationTitle::all(),
            'url_banner' => ConstantSystem::getUrlBanner(),
        ]);
    }

    public function update(UpdateSystemRequest $systemRequest)
    {
        System::first()->update($systemRequest->validated());
        Artisan::call('queue:restart');
        return redirect()->back()->with(['message' => 'System updated']);
    }
}

<?php

namespace Modules\System\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\System\Entities\MobileVersion;
use Modules\System\Entities\System;
use Modules\System\Http\Requests\MobileVersion\StoreMobileVersionRequest;
use Modules\System\Http\Requests\MobileVersion\UpdateIndexMobileVersionRequest;
use Modules\System\Http\Requests\MobileVersion\UpdateMobileVersionRequest;
use Modules\System\Http\Requests\UpdateSystemRequest;

class AdminMobileVersionController extends Controller
{
    public function updateIndex()
    {
        $versions = MobileVersion::where(function ($where) {
            $where->where('app_name', MobileVersion::ANDROID_PLATFORM)
                ->orWhere('app_name', MobileVersion::IOS_PLATFORM);
        })
            ->get();

        $data = [
            'versions' => $versions,
        ];

        return view('system::mobile-versions.update-index', $data);
    }

    public function updateIndexDB(UpdateIndexMobileVersionRequest $request)
    {
        $validated = $request->validated();

        MobileVersion::where('id', $validated['android_id'])->update([
            'version' => $validated['android_version'],
            'is_forced' => $validated['android_is_forced'],
//            'store_link' => $validated['android_store_link'],
        ]);

        MobileVersion::where('id', $validated['ios_id'])->update([
            'version' => $validated['ios_version'],
            'is_forced' => $validated['ios_is_forced'],
//            'store_link' => $validated['ios_store_link'],
        ]);

        $versions = MobileVersion::where(function ($where) {
            $where->where('app_name', MobileVersion::ANDROID_PLATFORM)
                ->orWhere('app_name', MobileVersion::IOS_PLATFORM);
        })
            ->get();

        return redirect()->back()->with(['message' => 'System updated']);
//        return view('system::mobile-versions.update-index', $data);
    }

    public function index()
    {
        $versions = MobileVersion::query();

        $versions = $this->filterQuery($versions);

        $data = [
            'versions' => $versions->paginate(10),
        ];
        if (request('render')) {
            return view('system::mobile-versions.data', $data)->render();
        }
        return view('system::mobile-versions.index', $data);
    }

    public function create()
    {
        return view('system::mobile-versions.create');
    }

    public function store(StoreMobileVersionRequest $request)
    {
        $validated = $request->validated();
        MobileVersion::create($validated);
        return redirect()->route('admin.mobile.version.index')->with(['message' => 'Mobile Version Created']);
    }

    public function changeForced()
    {
        $is_forced = request()->is_forced;
        MobileVersion::where('id', request()->mobile_version)->update(['is_forced' => $is_forced]);
        return response()->json(['success' => 'Forced change successfully.']);
    }

    public function edit(MobileVersion $mobile_version)
    {
        $data = [
            'mobile_version' => $mobile_version,
        ];
        return view('system::mobile-versions.edit', $data);
    }

    public function update(UpdateMobileVersionRequest $request, MobileVersion $mobile_version)
    {
        $validated = $request->validated();
        $mobile_version->update($validated);
        return redirect()->route('admin.mobile.version.index')->with(['message' => 'Mobile Version Updated']);
    }

    private function filterQuery(Builder $versions)
    {
        $query = request('query');
        $platform = request('platform');
        if (strlen($query)) {
            $versions->where(function ($q) use ($query) {
                $q->where('id', 'like', '%' . $query . '%')
                    ->orWhere('app_name', 'like', '%' . $query . '%')
                    ->orWhere('platform', 'like', '%' . $query . '%')
                    ->orWhere('version', 'like', '%' . $query . '%')
                    ->orWhere('link', 'like', '%' . $query . '%')
                    ->orWhere('store_link', 'like', '%' . $query . '%');
            });
        }
        if (in_array($platform, [MobileVersion::ANDROID_PLATFORM, MobileVersion::IOS_PLATFORM])) {
            $versions->where('platform', $platform);
        }
        return $versions;
    }

}

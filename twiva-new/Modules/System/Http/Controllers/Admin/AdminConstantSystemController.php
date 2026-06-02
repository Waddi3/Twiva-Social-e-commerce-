<?php

namespace Modules\System\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\System\Entities\ConstantSystem;

class AdminConstantSystemController extends Controller
{
    public function updateBanner(Request $request)
    {
        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $extension = $banner->getClientOriginalExtension();
            $banner_name = "banner.$extension";
            $path = $request->file('banner')->storeAs('e-commerce/images/banner', $banner_name, 'public');
            $key = 'banner';
            ConstantSystem::updateOrCreate(
                ['key' => $key],
                ['name' => 'banner', 'value' => $path]
            );
        }
        if ($request->has('url_banner') && filter_var($request->get('url_banner'), FILTER_VALIDATE_URL)) {
            $key = 'url_banner';
            ConstantSystem::updateOrCreate(
                ['key' => $key],
                ['name' => 'url banner', 'value' => $request->get('url_banner')]
            );
        }

        return redirect()->back()->with(['message' => 'Update Banner Info']);
    }
}

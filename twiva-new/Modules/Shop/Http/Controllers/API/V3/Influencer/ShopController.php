<?php

namespace Modules\Shop\Http\Controllers\API\V3\Influencer;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Requests\Influencer\EditShopRequest;
use Modules\Shop\Http\Resources\Shop\Influencer\ShowShopResource;
use Modules\Shop\Repositories\Influencer\ShopRepository;

class ShopController extends Controller
{
    public function show()
    {
        $shop = Shops::with('forInfluencers:id,name,email,type')->where('user_id', auth('api')->id())->first();
        if ($shop) {
            return response()->json(['data' => new ShowShopResource($shop)]);
        }
        return abort(403);

    }

    public function edit(EditShopRequest $request)
    {
        $data = $request->validated();
        ShopRepository::updateInfluencerShop($data);
        return response()->json(['data' => 'Shop Updated']);
    }

    public function deleteBackgroundImage()
    {
        $shop = Shops::where('user_id', auth('api')->id())->first();
        $shop->update(['shop_cover_image_path' => null]);
        return response()->json(['data' => 'Background Image Delete']);
    }


    public function preparePreviewLink(EditShopRequest $request)
    {
        $data = $request->validated();
        if (isset($data['shop_cover_image_path'])) {
            $data['shop_cover_image_path'] = $data['shop_cover_image_path']->store(Shops::TEMP_IMAGES_PATH, 'public');
            $data['shop_cover_image_path'] = asset(Storage::url($data['shop_cover_image_path']));
        }
        if (isset($data['shop_logo_path'])) {
            $data['shop_logo_path'] = $data['shop_logo_path']->store(Shops::TEMP_IMAGES_PATH, 'public');
            $data['shop_logo_path'] = asset(Storage::url($data['shop_logo_path']));
        }
        $domain = 'http://preview.twiva.sail:8030?data=';
        return response()->json([
//            'data' => $data,
            'link' => $domain . json_encode($data)]);

    }
}

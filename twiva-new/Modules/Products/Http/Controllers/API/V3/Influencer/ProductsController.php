<?php

namespace Modules\Products\Http\Controllers\API\V3\Influencer;

use Illuminate\Routing\Controller;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Products\Http\Resources\ProductImagesCollection;
use Modules\User\Entities\User;

class ProductsController extends Controller
{
    public function getProductImages()
    {
        $user = auth('api')->user();
        if ($user->type == User::InfluencerType) {
            $product = InfluencerShopProducts::where('influencer_id', $user->id)
                ->where('id', request('product_id'))
                ->with(
                    [
                        'influencerProductImages' => function ($influencerProductImagesQuery) {
                            $influencerProductImagesQuery->select('id', 'shop_product_id', 'image_path');
                        },
                        'BusinessProductImages' => function ($BusinessProductImagesQuery) {
                            $BusinessProductImagesQuery->select('id', 'image', 'product_id');
                        }
                    ])
                ->firstOrFail();
        } else {
            $product = Product::with('productImages')
                ->where('user_id', $user->id)
                ->where('id', request('product_id'))
                ->firstOrFail();
        }
        return response()->json(['data' => new ProductImagesCollection($product)]);
    }
}

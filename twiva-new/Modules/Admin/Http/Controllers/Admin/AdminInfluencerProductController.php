<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Influencer\Entities\InfluencerShopProducts;

class AdminInfluencerProductController extends Controller
{

    public function show($id)
    {
        $product = InfluencerShopProducts::with('productDetails', 'productMainImage', 'influencerProductImages', 'productDetails.productReviewsAndRatings')->where('id', $id)->first();
        return view('admin::influencerProduct.show',
            [
                'product' => $product,
                'influencer' => $product->influencer,
            ]);
    }

}

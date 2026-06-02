<?php

namespace Modules\Partner\Http\Controllers\API\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Influencer\Entities\Product;
use Modules\Products\Http\Resources\CatelogIndexCollection;

class PartnerProductController extends Controller
{
    public function index(Request $request) {
        $products = Product::select(
            'id', 'name', 'brand', 'price', 'retail_price', 'stock', 'main_image', 'total_sale',
            'discount', 'category', 'sub_category', 'status', 'has_shipping_fees', 'shipping_by'
        )
            ->where('stock', '>', 0)
            ->with(['productImages' => function ($query) {
                $query->select('id', 'image', 'is_cover_pic');
            }]);
        if ($request->has('keyword')) {
            $products = $products->where(function ($where) use ($request) {
                $where->where('name', 'like', "%$request->keyword%")
                    ->orWhere('brand', 'like', "%$request->keyword%");
            });
        }
        if ($request->has('subcategories')) {
            $products = $products->whereIn('sub_category', request('subcategories'));
        }
        if ($request->has('categories')) {
            $products = $products->whereIn('sub_category', request('categories'));
        }
        if ($request->has('is_published')) {
            $products = $products->where('status', '1');
        }
        if (in_array($request->priceOrder, ['ASC', 'DESC'])) {
            $products = $products->orderBy('price', $request->priceOrder);
        } else {
            $products->latest();
        }

        return response()->json(['data' => CatelogIndexCollection::collection($products->paginate(15))->resource]);
        //return $products;
    }

}

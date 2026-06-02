<?php

namespace Modules\Shop\Http\Controllers\API\V3\Influencer;

use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Influencer\Entities\InfluencerDetails;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Exports\ProductCatalogueExport;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Resources\Catelog\IndexProductsToAddCollection;
use Modules\Shop\Http\Resources\Shop\Influencer\ShowProductResource;

class ProductController extends Controller
{
    private function getShopID()
    {
        return Shops::select('id', 'user_id')->where('user_id', auth('api')->id())->first()->id;
    }

    public function index()
    {
        $shop_products_id = InfluencerShopProducts::where('shop_id', $this->getShopID())
            ->pluck('product_id')
            ->toArray();
        $products = Product::where('stock', '>', '0')
            ->whereNotIn('id', $shop_products_id)
            ->where('status', 1)
//            ->where('user_id', 4351)
            ->select('id', 'name', 'price', 'retail_price', 'main_image', 'category', 'color', 'discount',
                'size', 'specification', 'sub_category', 'created_at', 'description', 'brand');


        $products->where(function ($where_influencer) {
            $where_influencer->whereDoesntHave('user.shop.forInfluencers')
                ->orWhereHas('user.shop.forInfluencers', function ($where_has_influencer) {
                    $where_has_influencer->where('user_id', auth('api')->id());
                });
        });

        $keyword = request('keyword');
        $category = request('categories');
        $subcategory = request('subcategories');
        $priceOrder = request('priceOrder');
        if ($keyword) {
            $products->where(function ($where) use ($keyword) {
                $where->where('name', 'like', "%$keyword%")->orWhere('brand', 'like', "%$keyword%");
            });
        }
        if ($category) {
            $products = $products->whereIn('category', $category);
        }
        if ($subcategory) {
            $products = $products->whereIn('sub_category', $subcategory);
        }
        if ($priceOrder) {
            $products = $products->orderBy('price', $priceOrder);
        }
        if (!$priceOrder) {
            $products = $products->inRandomOrder();
        }
        return response()->json(['data' => IndexProductsToAddCollection::collection($products->paginate(20))->resource]);
    }

    public function show()
    {
        $product = Product::where('id', request('product_id'))
            ->where('status', 1)
            ->with(['productImages'])
            ->select('id', 'name', 'description', 'price', 'retail_price', 'main_image', 'category', 'sub_category', 'color', 'size', 'stock', 'specification', 'discount', 'brand')
            ->firstOrFail();
        return response()->json(['data' => new ShowProductResource($product)]);
    }

    public function exportCSV($influencer_id)
    {
        $infl_id = base64_decode(base64_decode($influencer_id));
        $infl_details = InfluencerDetails::where('influencer_id', $infl_id)->firstOrFail();
        return Excel::download(new ProductCatalogueExport($infl_id), 'products_catalogue.csv');


    }
}

<?php

namespace Modules\Shop\Http\Controllers\API\V3\Business;

use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Business\Entities\Company;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Exports\BusinessProductCatalogueExport;
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
            ->select('id', 'name', 'price', 'retail_price', 'main_image', 'category', 'color',
                'size', 'specification', 'sub_category', 'created_at', 'description');
        $keyword = request('keyword');
        $category = request('categories');
        $subcategory = request('subcategories');
        $priceOrder = request('priceOrder');
        if ($keyword) {
            $products->where('name', 'like', '%' . $keyword . '%');
        }
        if ($category) {
            $products = $products->whereIn('category', $category);
        }
        if ($subcategory) {
            $products = $products->whereIn('sub_category', $subcategory);
        }
        //order by price
        // TODO::H check retail_price
        if ($priceOrder) {
            $products = $products->orderBy('price', $priceOrder);
        } else {
            $products = $products->orderBy('created_at', 'DESC');
        }
        return response()->json(['data' => IndexProductsToAddCollection::collection($products->paginate(20))->resource]);
    }

    public function show()
    {
        $product = Product::where('id', request('product_id'))
            ->where('status', 1)
            ->with(['productImages'])
            ->select('id', 'name', 'description', 'price', 'retail_price', 'main_image', 'category', 'sub_category', 'color', 'size', 'stock', 'specification')
            ->firstOrFail();
        return response()->json(['data' => new ShowProductResource($product)]);
    }

    public function exportCSV($business_id)
    {
        $business_id = base64_decode(base64_decode($business_id));
        return Excel::download(new BusinessProductCatalogueExport($business_id), 'products_catalogue.csv');
    }
}

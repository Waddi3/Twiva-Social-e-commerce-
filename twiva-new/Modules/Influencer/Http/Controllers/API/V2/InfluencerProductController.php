<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductReviews;

// use Validator,Mail,File,URL;

class InfluencerProductController extends Controller
{
    public function getListableProducts(Request $request)
    {
        $user = auth('api')->user();
        if ($user) {
            try {
                $input = $request->all();
                $validator = Validator::make($input, [
                    'limit' => 'numeric | nullable',
                    'page' => 'numeric | nullable',
                ]);
                if ($validator->fails()) {
                    $ret = array('success' => 0, 'message' => $validator->messages()->first());
                    return response()->json($ret);
                }
                $limit = $request->limit ? $request->limit : 10;
                $influencerShopProduct = InfluencerShopProducts::where('influencer_id', $user->id)->pluck('product_id')->toArray();
                if (count($influencerShopProduct) > 0) {
                    $products = Product::where('status', 1)->whereNotIn('id', $influencerShopProduct);
                } else {
                    $products = Product::where('status', 1);
                }
                if ($request->has('categories') && $input['categories'] != "") {
                    $categories = explode(',', $input['categories']);
                    $products->whereIn('products.category', $categories);
                    // $products->whereRaw("FIND_IN_SET($categories[0],products.category)");
                    // foreach($categories as $value){
                    //     $products->orWhereRaw("FIND_IN_SET($value,products.category)");
                    // }
                }
                if (($request->has('categories') && $input['categories'] != "") && ($request->has('sub_categories') && $input['sub_categories'] != "")) {
                    $sub_categories = explode(',', $input['sub_categories']);
                    $products->whereIn('sub_category', $sub_categories);
                    // $products->whereRaw("FIND_IN_SET($sub_categories[0],products.sub_category)");
                    // foreach($sub_categories as $value){
                    //     $products->orWhereRaw("FIND_IN_SET($value,products.sub_category)");
                    // }
                }
                // if(($request->has('price_min') && $request->has('price_max')) && ($input['price_min'] !== "" && $input['price_max'] !== "")){
                //     $products->whereBetween('price',[$input['price_min'],$input['price_max']]);
                // }
                if ($request->has('search') && $input['search'] != "") {
                    $products->where('name', 'like', '%' . $input['search'] . '%');
                }
                $products->with(['productImages']);
                if (($request->has('sort_by') && $input['sort_by'] != "")) {
                    if ($input['sort_by'] == 0) {
                        $products->orderBy('price', 'asc');
                    } else {
                        $products->orderBy('price', 'desc');
                    }
                } else {
                    $products->orderBy('id', 'desc');
                }
                $products = $products->paginate($limit)->toArray();
                foreach ($products['data'] as $key => $product) {
                    $product_reviews_and_ratings = ProductReviews::with('buyer:buyer_id,name')->where('product_id', $product['id'])->orderBy('id', 'desc')->paginate($limit)->toArray();
                    $products['data'][$key]['product_reviews_and_ratings'] = $product_reviews_and_ratings['data'];
                }

                return response()->json(array('success' => true, 'data' => $products['data'], 'current_page' => $products['current_page'], 'last_page' => $products['last_page'], 'total_results' => $products['total'], 'message' => "Product Listing"), 200, []);
            } catch (Exception $e) {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }

    }


    public function uploadImage(Request $request)
    {
        if (request()->file('image')) {
            $path = request()->file('image')->store('public/images/products');
            $path = str_replace('public/images/products', '', $path);
//            $path = 'images/products';
//            $path = Storage::putFile($path, request()->file('image'));
//            $path = str_replace('images/products', '', $path);
            return response(['code' => 200, 'image' => $path, "message" => 'stored successfully'], 200);
        } else {
            return response(['code' => 404, "message" => 'No file sent'], 404);
        }
    }

    public function addProduct(Request $request)
    {
        $user = Auth::user();
        $date = Carbon::now();
        if ($user) {
            try {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'shop_id' => 'required',
                    'products' => 'required | array | min:1',
                    'products[*].shop_id' => 'required',
                    'products[*].product_id' => 'required | exists:products,id',
                    'products[*].image' => 'required | array',
                    'products[*].title' => 'required | string',
                    'products[*].price' => 'required | integer',
                    'products[*].description' => 'required | string'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'error' => $validator->errors()], 400);
                }
                $products = $data['products'];
                foreach ($products as $product) {

                    $product['shop_id'] = $data['shop_id'];
                    $product['influencer_id'] = $user->id;
                    $productImages = $product['product_images'];
                    unset($product['product_images']);
                    $productImgArray = array();
                    $influencerShopProduct = InfluencerShopProducts::create($product);

                    foreach ($productImages as $productImage) {
                        if (isset($productImage['image'])) {
                            $productImgArray[] = array("shop_product_id" => $influencerShopProduct->id, 'image_path' => $productImage['image'], 'is_cover_pic' => $productImage['is_cover_pic'], 'created_at' => $date, 'updated_at' => $date);
                        }
                    }
                    if (count($productImgArray) > 0) {
                        InfluencerShopProductImages::insert($productImgArray);
                    }
                }
                $success_message = array('message' => "Product added.", 'status' => true);
                return response()->json($success_message, 200);
            } catch (Exception $e) {
                return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
            }
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }
    }

}

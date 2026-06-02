<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Shop\Entities\Shops;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\ProductReviews;

// use Validator,Mail,File,URL;

class InfluencerShopController extends Controller
{
    public function getListableShops()
    {
        $user = auth('api')->user();
        if ($user)
        {
            try
            {
                $shops = Shops::where('user_id', $user->id)->get();
                $success_message = array('message' => "Shops listing.", "data" => $shops, 'status' => true);
                return response()->json($success_message, 200);
            }
            catch (Exception $e)
            {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        }
        else
        {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }

    }

    public function viewShopProducts(Request $request, $shop_id)
    {
        $userId = auth('api')->id();
        try
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'limit' => 'numeric | nullable',
                'page' => 'numeric | nullable',
            ]);
            if ($validator->fails())
            {
                $ret = array('success' => 0, 'message' => $validator->messages()->first());
                return response()->json($ret);
            }
            $limit = $request->limit ? $request->limit : 10;
            $products = InfluencerShopProducts::where('shop_id', $shop_id)->where('influencer_id', $userId);
            $products->with(['influencerProductImages', 'productDetails']);
            if ($request->has('search') && $input['search'] != "")
            {
                $products->where('product_title', 'like', '%' . $input['search'] . '%');
            }
            if ($request->has('categories') && $input['categories'] != "")
            {
                $categories = explode(',', $input['categories']);
                $products->whereHas('productDetails', function ($query) use ($categories) {
                    $query->whereIn('category', $categories);
                });
            }
            if (($request->has('categories') && $input['categories'] != "") && ($request->has('sub_categories') && $input['sub_categories'] != ""))
            {
                $sub_categories = explode(',', $input['sub_categories']);
                foreach ($sub_categories as $value)
                {
                    $products->whereHas('productDetails', function ($query) use ($value) {
                        $query->whereRaw("FIND_IN_SET($value,products.sub_category)");
                    });
                }
            }
            if (($request->has('sort_by') && $input['sort_by'] != ""))
            {
                if ($input['sort_by'] == 0)
                {
                    $products->orderBy('product_price', 'asc');
                }
                else
                {
                    $products->orderBy('product_price', 'desc');
                }
            }
            else
            {
                $products->orderBy('id', 'desc');
            }
            $products = $products->paginate($limit)->toArray();

            foreach ($products['data'] as $key => $product)
            {
                $product_reviews_and_ratings = ProductReviews::with('buyer:buyer_id,name')->where('product_id', $product['product_id'])->orderBy('id', 'desc')->paginate($limit)->toArray();
                $products['data'][$key]['product_reviews_and_ratings'] = $product_reviews_and_ratings['data'];
            }
            // return response()->json(array('status'=>true,'message'=>"Shops detail fetched",'shop_product_details'=>$products['data'],'current_page'=>$products['current_page'],'last_page'=>$products['last_page'],'total_results'=>$products['total']) ,200,[],JSON_NUMERIC_CHECK);

            $success_message = array('message' => "Shops detail fetched.", 'shop_product_details' => $products, 'status' => true);
            return response()->json($success_message, 200);
        }
        catch (Exception $e)
        {
            $error_message = array('error' => $e->getMessage(), 'status' => false);
            return response()->json($error_message, 500);
        }
    }

    public function editShop(Request $request)
    {
        $shop = Shops::where('user_id', auth('api')->id())->firstOrFail();
        try
        {
            $input = $request->all();
            $validator = Validator::make($input, [
                'shop_name' => 'required | string',
                'shop_cover_image_path' => 'required | string',
                'shop_logo_path' => 'required | string',
                'shop_description' => 'required',
                'shop_color' => 'nullable',
                'domain' => [
                    Rule::requiredIf($shop->domain == null)],
                'regex:/^[0-9a-z][a-z0-9]*/', 'alpha_dash', 'min:4', 'string'
            ]);
            $validator->sometimes('domain', Rule::unique('shops', 'domain')->ignore(auth('api')->id(), 'user_id'),
                function ($input) {
                    return !empty($input->domain);
                }
            );
            if ($validator->fails())
            {
                $ret = array('success' => 0, 'message' => $validator->messages()->first());
                return response()->json($ret, 422);
            }
            $shop->update($input);
            $success_message = array('status' => true, 'message' => "Shop updated successfully");
            return response()->json($success_message, 200);


        }
        catch (Exception $e)
        {
            $error_message = array('error' => $e->getMessage(), 'status' => false);
            return response()->json($error_message, 500);
        }
    }
}

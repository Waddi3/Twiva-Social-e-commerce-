<?php

namespace Modules\Business\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;
use Modules\Influencer\Entities\ProductReviews;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\User\Entities\UserSubscription;

class ApiBusinessProductController extends Controller
{
    public function addProduct(Request $request)
    {
        try
        {
            $free_product_limit = 10;
            $date = Carbon::now();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required',
                'retail_price' => ['required', 'gte:price'],
                'category' => 'required',
                'stock' => 'required'
            ]);
            if ($validator->fails())
            {
                return response()->json(['status' => false, 'message' => $validator->messages()->first()], 404);
            }
            $input = $request->all();
            $userDetails = auth('api')->user();
            if (!$userDetails)
            {
                $error_message = array('message' => "Unauthorized", 'status' => false);
                return response()->json($error_message, 401);
            }

            $user_product_count = Product::where('user_id', $userDetails->id)->count();
            $today = Carbon::now();
            $date2 = Carbon::createFromFormat('Y-m-d H:i:s', $today);
            $input['status'] = 1;
            $input['user_id'] = $userDetails['id'];
            $input['color'] = $input['color_ids'];
            $input['size'] = $input['size_ids'];
            $product = Product::create($input);
            if (isset($input['product_images']))
            {
                $productImgArray = array();
                $productImages = $input['product_images'];
                foreach ($productImages as $productImage)
                {
                    $productImgArray[] = array("product_id" => $product->id, 'image' => $productImage['image'], 'is_cover_pic' => $productImage['is_cover_pic'], 'created_at' => $date, 'updated_at' => $date);
                }
                if (count($productImgArray) > 0)
                {
                    ProductImage::insert($productImgArray);
                }
            }
            return response()->json(['status' => true, 'message' => 'Product added successfully'], 200);


        }
        catch (Exception $e)
        {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }

    }

    public function inviteStatus(Request $request)
    {
        $data = Product::where('id', $request->user_id)->update(['invite' => filter_var($request->status, FILTER_VALIDATE_BOOLEAN)]);
        return response()
            ->json([
                'success' => true,
                'message' => "Invitation successfully updated",
                'data' => $request->status
            ],
                200,
                [],
                JSON_NUMERIC_CHECK
            );
    }

    public function fetchProducts(Request $request)
    {
        try
        {
            $input = $request->all();
            $userDetails = auth('api')->user();

            if (!$userDetails)
            {
                $error_message = array('message' => "Unauthorized", 'status' => false);
                return response()->json($error_message, 401);
            }

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
            $products = Product::where('user_id', $userDetails->id);
            if ($request->has('categories') && $input['categories'] != "")
            {
                $categories = explode(',', $input['categories']);
                $products->whereIn('products.category', $categories);
            }
            if (($request->has('categories') && $input['categories'] != "") && ($request->has('sub_categories') && $input['sub_categories'] != ""))
            {
                $sub_categories = explode(',', $input['sub_categories']);
                $products->whereIn('sub_category', $sub_categories);
            }
            if ($request->has('search') && $input['search'] !== "")
            {
                $products->where('name', 'like', '%' . $input['search'] . '%');
            }
            $products->with(['productImages']);
            if (($request->has('sort_by') && $input['sort_by'] !== ""))
            {
                if ($input['sort_by'] == 0)
                {
                    $products->orderBy('price', 'asc');
                }
                else
                {
                    $products->orderBy('price', 'desc');
                }
            }
            else
            {
                $products->orderBy('id', 'desc');
            }

            $products = $products->paginate($limit)->toArray();

            return response()->json(array('success' => true, 'data' => $products['data'], 'current_page' => $products['current_page'], 'last_page' => $products['last_page'], 'total_results' => $products['total'], 'message' => "Product Listing"), 200, []);

        }
        catch (Exception $e)
        {
            return response()->json(['failure' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function reportDetails()
    {
        $user = auth('api')->user();
        $type = request('type');
        if ($type == '1')
        {
            // best performing
            $top_best_products = Product::with('productImages')
                ->where('user_id', $user->id)
                ->where('total_sale', '>', '0')
                ->orderBy('total_sale', 'desc')
                ->limit(10)
                ->get();
            foreach ($top_best_products as $product)
            {
                $product->category = $product->category_details;
                $product->sub_category = $product->subcategory_details;
            }
            $success_message = ['status' => true, 'message' => "Product Listing", 'data' => $top_best_products];
            return response()->json($success_message, 200);
        }
        // least performing
        else if ($type == '2')
        {
            $least_selling_products = Product::with('productImages')
                ->where('user_id', $user->id)
                ->orderBy('total_sale', 'asc')
                ->limit(10)
                ->get();
            $success_message = ['status' => true, 'message' => "Product Listing", 'data' => $least_selling_products];
            return response()->json($success_message);
        }
        else if ($type == '3')
        {
            $revenue = [];
            $amountInYear = 0;
            $ordersCount = 0;
            for ($i = 1; $i <= 12; $i++)
            {
                $startDate = date("Y") . '-' . $i . '-01';
                $month = date('M', strtotime($startDate));
                $amountInMonth = 0;
                $ordersInEachMonth = Order::where('business_id', $user->id)
                    ->where('order_status', '=', Order::DELIVERED_STATUS)
                    ->whereYear('created_at', '=', date("Y"))
                    ->whereMonth('created_at', $i)
                    ->get();
                if ($ordersInEachMonth->isNotEmpty())
                {
                    $amountInMonth = $ordersInEachMonth->sum('business_amount');
                    $amountInYear += $amountInMonth;
                    $ordersCount++;
                }
                $data = ["month" => $month, "income" => $amountInMonth];
                $revenue[] = $data;
            }

            $success_message = ['status' => true, 'message' => "Monthly Sale", 'revenue' => $revenue,
                'sale_order' =>
                    [
                        'total_order' => $ordersCount,
                        'total_sale' => $amountInYear
                    ]];
            return response()->json($success_message);

        }
        else
        {
            $error_message = array('status' => false, 'message' => "Please provide valid type");
            return response()->json($error_message, 401);
        }

    }

    public function productDetail(Request $request, $id)
    {
        try
        {
            $userDetails = auth('api')->user();
            if (!$userDetails)
            {
                $error_message = array('message' => "Unauthorized", 'status' => false);
                return response()->json($error_message, 401);
            }
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
            $products = Product::with('productImages')->where('id', $id)->first();
            if ($products)
            {
                $product_reviews_and_ratings = ProductReviews::with('buyer:buyer_id,name')->where('product_id', $id)->orderBy('id', 'desc')->paginate($limit)->toArray();
                $products->product_reviews_and_ratings = $product_reviews_and_ratings['data'];
            }
            else
            {
                $products->product_reviews_and_ratings = [];
            }
            return response()->json(array('success' => true, 'message' => "Product Details", 'data' => $products), 200, []);

        }
        catch (Exception $e)
        {
            return response()->json(['failure' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function editProduct(Request $request, $id)
    {
        try
        {

            $date = Carbon::now();

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'price' => 'required',
                'retail_price' => ['required', 'gte:price'],
                'category' => 'required',
                'stock' => 'required'
            ]);

            if ($validator->fails())
            {
                return response()->json(['status' => false, 'message' => $validator->messages()->first()], 404);
                //return response()->json(['status'=>$this->validateInputs,'error'=>$validator->errors()], $this->validateInputs);
            }

            $input = $request->all();
            $userDetails = auth('api')->user();

            if (!$userDetails)
            {
                $error_message = array('message' => "Unauthorized", 'status' => false);
                return response()->json($error_message, 401);
            }

            $input = $request->all();
            $userDetails = auth('api')->user();
            $products = Product::where('user_id', $userDetails->id)->where('id', $id)->first();
            if ($products)
            {

                $products->name = $input['name'];
                $products->price = $input['price'];
                $products->retail_price = $input['retail_price'];
                $products->sub_category = $input['sub_category'];
                $products->category = $input['category'];
                $products->stock = $input['stock'];
                $products->description = $input['description'];
                $products->sku = $input['sku'];
                $products->specification = $input['specification'];
                $products->color = $input['color_ids'];
                $products->size = $input['size_ids'];
                $products->save();


                if (isset($input['product_images']))
                {
                    ProductImage::where('product_id', $products->id)->delete();
                    $productImgArray = array();
                    $productImages = $input['product_images'];
                    foreach ($productImages as $productImage)
                    {
                        $productImgArray[] = array("product_id" => $products->id, 'image' => $productImage['image'], 'is_cover_pic' => $productImage['is_cover_pic'], 'created_at' => $date, 'updated_at' => $date);
                    }
                    if (count($productImgArray) > 0)
                    {
                        ProductImage::insert($productImgArray);
                    }
                }

                return response()->json(array('success' => true, 'message' => "Product successfully updated"), 200, []);
            }

            return response()->json(array('success' => false, 'message' => "Invalid Productid"), 404, []);

        }
        catch (Exception $e)
        {
            return response()->json(['failure' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function deleteProduct(Request $request, $id)
    {
        try
        {
            $input = $request->all();
            $userDetails = auth('api')->user();
            if (!$userDetails)
            {
                $error_message = array('message' => "Unauthorized", 'status' => false);
                return response()->json($error_message, 401);
            }
            $products = Product::where('user_id', $userDetails->id)->where('id', $id)->first();
            if ($products)
            {
                $products->delete();
                InfluencerShopProducts::where('product_id', $id)->delete();
                return response()->json(array('success' => true, 'message' => "Product successfully deleted"), 200, []);
            }
            return response()->json(array('success' => false, 'message' => "Invalid Productid"), 404, []);

        }
        catch (Exception $e)
        {
            return response()->json(['failure' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function publishUnpublish(Request $request)
    {
        try
        {
            $input = $request->all();
            $rules = array(
                'status' => 'required|integer',
                'product_id' => 'required'

            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails())
            {
                return response()->json(['error' => $validator->messages()->first()], 401);
            }
            $user_id = auth('api')->id();
            $product = Product::where('id', $input['product_id'])->where('user_id', $user_id)->first();
            $user_product_count = Product::where('user_id', $user_id)->where('status', 1)->count();
            $user_old_subscription = UserSubscription::with('subscriptionDetails')->where('user_id', $user_id)->where('is_expiry', 0)->first();
            $product_limit = $user_old_subscription->subscriptionDetails->max_products;
            $user_old_end_date = $user_old_subscription->end_date;
            $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $user_old_end_date);
            $today = Carbon::now();
            $date2 = Carbon::createFromFormat('Y-m-d H:i:s', $today);
            if (!$date1->gt($date2))
            {
                return response()->json(['success' => false, 'message' => 'Your Subscription has expired. You cannot publish/unpublish product'], 400);
            }
            if ($date1->gt($date2) && ($user_product_count > $product_limit || $user_product_count == $product_limit) && $input['status'] == 1)
            {
                return response()->json(['success' => false, 'message' => 'Your Product limit has reached'], 400);
            }

            if ($product)
            {
                if ($input['status'] == 1 || $input['status'] == 0)
                {
                    $product->status = $input['status'];
                    $product->save();
                }
                else
                {
                    return response()->json(['success' => false, 'message' => 'Only  0,1 values are allowed for status'], 400);
                }
            }
            else
            {
                return response()->json(['success' => false, 'message' => 'product not found'], 400);
            }
            if ($input['status'] == 1)
            {
                $success = 'Product published successfully';
            }
            else
            {
                $success = 'Product unpublished successfully';
            }
            return response()->json(['success' => true, 'message' => $success], 200);
        }
        catch (Exception $e)
        {
            return response()->json(['failure' => 500, 'error' => $e->getMessage()]);
        }
    }

}

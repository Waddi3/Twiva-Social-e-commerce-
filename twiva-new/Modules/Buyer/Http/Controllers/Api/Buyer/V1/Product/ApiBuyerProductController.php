<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1\Product;

use Modules\Buyer\Http\Resources\Api\Buyer\ApiBuyerProductShowResource;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Shops;
use Modules\Products\Http\Requests\ECommerce\ECommerceCategorySuggestionRequest;
use Modules\Products\Http\Requests\ECommerce\ECommerceHotDealProductRequest;
use Modules\Products\Http\Requests\ECommerce\ECommerceProductIndexRequest;
use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;
use Modules\Products\Repositories\ECommerce\ECommerceProductRepository;
use Modules\System\Entities\ConstantSystem;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerProductController
{
    public function __construct(public ECommerceProductRepository $e_commerce_product_repository,
                                public ECommerceCategoryRepository $e_commerce_category_repository)
    {
    }
    public function products(ECommerceProductIndexRequest $request)
    {
        $has_next_page = true;
        $start_time = now();
        $filter = $request->validated();

        $account = removeDotFromSubDomain(request('account'));
        if ($account) {
            $shop_user = Shops::select('id', 'user_id')->with('user:id,type')->where('domain', $account)->first();
            $filter['user_type'] = $shop_user?->user?->type;
            $filter['user_id'] = $shop_user?->user?->id;
        }

        if (isset($filter['user_id'])) {
            [$products, $products_counts, $influencer_products_counts, $min_max_price, $product_last_page, $influencer_product_last_page, $sub_categories] = $this->e_commerce_product_repository->products($filter);
            $counts = $products_counts + $influencer_products_counts;
            $has_next_page = request('page', 1) < $influencer_product_last_page || request('page', 1) < $product_last_page;
        } else {
            [$products, $products_counts] = $this->e_commerce_product_repository->productsNewAlgorithm($filter);
            $counts = $products_counts;
            $has_next_page = max(1, ceil($counts / $filter['per_page'])) >= $filter['page'];
        }

        $end_time = now();

        return response()->json([
            'time' => $end_time->diffInMilliseconds($start_time) . " ms",
            'counts' => $counts,
            'products_counts' => $products_counts,
            'influencer_products_counts' => $influencer_products_counts ?? 0,
            'min_price' => isset($min_max_price['min']) ? floor($min_max_price['min']) : 0,
            'max_price' => isset($min_max_price['max']) ? ceil($min_max_price['max']) : 0,
            'has_next_page' => $has_next_page,
            'products' => $products,
            'sub_categories' => $sub_categories,
        ]);
    }

    public function hotDeals(ECommerceHotDealProductRequest $request)
    {
        $filter = $request->validated();
        $account = removeDotFromSubDomain(request('account'));
        if ($account) {
            $shop_user = Shops::select('id', 'user_id')->with('user:id,type')->where('domain', $account)->first();
            $filter['user_type'] = $shop_user?->user?->type;
            $filter['user_id'] = $shop_user?->user?->id;
        }

        if (isset($filter['user_id'])) {
            $products = $this->e_commerce_product_repository->productsHotDeals($filter);
        } else {
            [$products, $_] = $this->e_commerce_product_repository->hotDealsNewAlgorithm($filter);
        }
        return response()->json([
            'products' => $products,
        ]);
    }

    public function productsMightLike(ECommerceProductIndexRequest $request)
    {
        $has_next_page = true;
        $filter = $request->validated();

        $account = removeDotFromSubDomain(request('account'));
        if ($account) {
            $shop_user = Shops::select('id', 'user_id')->with('user:id,type')->where('domain', $account)->first();
            $filter['user_type'] = $shop_user?->user?->type;
            $filter['user_id'] = $shop_user?->user?->id;
        }

        if (isset($filter['user_id'])) {
            [$products, $products_counts, $influencer_products_counts, $min_max_price, $product_last_page, $influencer_product_last_page, $sub_categories] = $this->e_commerce_product_repository->products($filter);
            $counts = $products_counts + $influencer_products_counts;
            $has_next_page = request('page', 1) < $influencer_product_last_page || request('page', 1) < $product_last_page;
        } else {
            [$products, $products_counts] = $this->e_commerce_product_repository->productsNewAlgorithm($filter);
            $counts = $products_counts;
            $has_next_page = max(1, ceil($counts / $filter['per_page'])) >= $filter['page'];
        }

        return response()->json([
            'counts' => $counts,
            'products_counts' => $products_counts,
            'influencer_products_counts' => $influencer_products_counts ?? 0,
            'min_price' => isset($min_max_price['min']) ? floor($min_max_price['min']) : 0,
            'max_price' => isset($min_max_price['max']) ? ceil($min_max_price['max']) : 0,
            'has_next_page' => $has_next_page,
            'products' => $products/*->paginate($filter['per_page'])*/,
        ]);
    }

    public function show($account, $id)
    {

        // $buyer = auth('buyer-api')->user();
        // $user = User::find($buyer->created_by);

        $user = User::fromShop($account);

        $with_similar = true;
        [$product, $similar_products] = Product::product($user, $id, $with_similar);
        // dd($product, $similar_products);
        if (is_null($product)) {
            return response()->json(['error' => 'selected product not found'], Response::HTTP_BAD_REQUEST);
        }
        // return $products;
        return new ApiBuyerProductShowResource($product, $similar_products);
    }

    public function featuredCategories(ECommerceCategorySuggestionRequest $request)
    {
        $filter = $request->validated();
        $categories_suggestions = $this->e_commerce_category_repository->getSubCategories($filter);
        return response()->json([
            'categories_suggestions' => $categories_suggestions
        ]);
    }

    public function getBanner() {
        $banner = ConstantSystem::getBanner();
        if($banner) {
            return response()->json([
                'status' => true,
                'banner' => $banner
            ]);
        }
        return response()->json([
            'status' => false,
        ]);

    }
}

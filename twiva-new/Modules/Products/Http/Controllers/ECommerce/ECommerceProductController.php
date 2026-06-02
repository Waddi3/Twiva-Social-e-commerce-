<?php

namespace Modules\Products\Http\Controllers\ECommerce;

use App\Http\Controllers\Controller;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Shops;
use Modules\Products\Http\Requests\ECommerce\ECommerceHotDealProductRequest;
use Modules\Products\Http\Requests\ECommerce\ECommerceProductIndexRequest;
use Modules\Products\Http\Requests\ECommerce\ECommerceTrendingProductRequest;
use Modules\Products\Repositories\ECommerce\ECommerceProductRepository;
use Modules\User\Entities\User;

class ECommerceProductController extends Controller {

    public function __construct(public ECommerceProductRepository $e_commerce_product_repository)
    {
    }

    public function hotDeals(ECommerceHotDealProductRequest $request)
    {
        $start_time = now();
        $filter = $request->validated();
        $filter = self::checkSubdomainAccount($filter);

        $cache_key = __FUNCTION__ . md5(json_encode($filter));
        $products = cache()->remember($cache_key, 60 * config('twiva.e-commerce.cache_time_per_minutes'), function () use ($filter)
        {
            if (isset($filter['user_id']))
            {
                $products = $this->e_commerce_product_repository->productsHotDeals($filter);
                $products->map(function ($product)
                {
                    $product->render = Product::renderCardForShop($product);
                    return $product;
                });
            }
            else
            {
                [$products, $_] = $this->e_commerce_product_repository->hotDealsNewAlgorithm($filter);
                $products->map(function ($product)
                {
                    $product->render = Product::renderCardForECommerce($product);
                    return $product;
                });
            }
            return $products;
        });
        $end_time = now();
        return response()->json([
            'time'     => $end_time->diffInMilliseconds($start_time) . " ms",
            'products' => $products,
        ]);
    }

    public function trendingProducts(ECommerceTrendingProductRequest $request)
    {
        $start_time = now();
        $filter = $request->validated();
        $cache_key = __FUNCTION__ . md5(json_encode($filter));
        [$products, $_] = cache()->remember($cache_key, 60 * config('twiva.e-commerce.cache_time_per_minutes'), function () use ($filter)
        {
            return $this->e_commerce_product_repository->trendingNewAlgorithm($filter);
        });
        $products->map(function ($product)
        {
            $product->render = Product::renderCardForECommerce($product);
            return $product;
        });

        $end_time = now();
        return response()->json([
            'time'     => $end_time->diffInMilliseconds($start_time) . " ms",
            'products' => $products,
        ]);
    }

    public function index(ECommerceProductIndexRequest $request)
    {
        $has_next_page = true;
        $start_time = now();
        $filter = $request->validated();
        $filter = self::checkSubdomainAccount($filter);
        $cache_key = __FUNCTION__ . md5(json_encode($filter));
        if (app()->environment('local'))
        {
            [$products, $counts, $has_next_page, $products_counts, $min_max_price] =
                $this->getIndexProductToCache($filter);
        }
        else
        {
            [$products, $counts, $has_next_page, $products_counts, $min_max_price] =
                \Cache::remember(key: $cache_key,
                    ttl: 60 * config('twiva.e-commerce.cache_time_per_minutes'),
                    callback: fn() => $this->getIndexProductToCache($filter));
        }
        $end_time = now();

        return response()->json([
            'time'                       => $end_time->diffInMilliseconds($start_time) . " ms",
            'counts'                     => $counts,
            'products_counts'            => $products_counts,
            'influencer_products_counts' => $influencer_products_counts ?? 0,
            'min_price'                  => isset($min_max_price['min']) ? floor($min_max_price['min']) : 0,
            'max_price'                  => isset($min_max_price['max']) ? ceil($min_max_price['max']) : 0,
            'has_next_page'              => $has_next_page,
            // 'has_next_page' => $products_counts ? true : false,
            'products'                   => $products,
        ]);
    }

    public static function checkSubdomainAccount($filter)
    {
        $account = removeDotFromSubDomain(request('account'));
        if ($account)
        {
            $shop_user = Shops::select('id', 'user_id')->with('user:id,type')->where('domain', $account)->first();
            $filter['user_type'] = $shop_user?->user?->type;
            $filter['user_id'] = $shop_user?->user?->id;
        }
        return $filter;
    }

    public function indexNewAlgorithm(ECommerceProductIndexRequest $request) // for test
    {
        $has_next_page = true;
        $start_time = now();
        $filter = $request->validated();

        $filter = self::checkSubdomainAccount($filter);

        // $influencer_products_counts, $min_max_price, $product_last_page, $influencer_product_last_page
        [$products, $products_counts] = $this->e_commerce_product_repository->productsNewAlgorithm($filter);

        $end_time = now();
        return response()->json([
            'time'          => $end_time->diffInMilliseconds($start_time) . " ms",
            'counts'        => $products_counts,
            'products'      => $products,
            'has_next_page' => $products_counts ? true : false,
        ]);
    }

    public function hotDealsNewAlgorithm(ECommerceHotDealProductRequest $request) // for test
    {
        $start_time = now();
        $filter = $request->validated();

        $filter = self::checkSubdomainAccount($filter);

        [$products, $products_counts] = $this->e_commerce_product_repository->hotDealsNewAlgorithm($filter);

        $end_time = now();
        return response()->json([
            'time'     => $end_time->diffInMilliseconds($start_time) . " ms",
            'products' => $products,
        ]);
    }

    public function trendingAlgorithm(ECommerceHotDealProductRequest $request) // for test
    {
        $start_time = now();
        $filter = $request->validated();

        $filter = self::checkSubdomainAccount($filter);

        [$products, $products_counts] = $this->e_commerce_product_repository->trendingNewAlgorithm($filter);

        $end_time = now();
        return response()->json([
            'time'     => $end_time->diffInMilliseconds($start_time) . " ms",
            'products' => $products,
        ]);
    }


    public function getIndexProductToCache($filter): array
    {
        if (isset($filter['user_id']))
        {
            [$products, $products_counts, $influencer_products_counts, $min_max_price, $product_last_page, $influencer_product_last_page] =
                $this->e_commerce_product_repository->products($filter);
            $counts = $products_counts + $influencer_products_counts;
            $products->map(function ($product)
            {
                $product->render = Product::renderCardForShop($product);
                return $product;
            });
            $has_next_page = request('page', 1) < $influencer_product_last_page || request('page', 1) < $product_last_page;
        }
        else
        {
            [$products, $products_counts] = $this->e_commerce_product_repository->productsNewAlgorithm($filter);
            $counts = $products_counts;
            $products->map(function ($product)
            {
                $product->render = Product::renderCardForECommerce($product);
                return $product;
            });
            // dd(max(1, ceil($counts / $filter['per_page'])), $counts, $filter['per_page'], $filter['page'], max(1, ceil($counts / $filter['per_page'])) >= $filter['page']);
            $has_next_page = max(1, ceil($counts / $filter['per_page'])) >= $filter['page']; // $products_counts ? true : false;
        }
        return [$products, $counts, $has_next_page, $products_counts, $min_max_price ?? []];
    }
}

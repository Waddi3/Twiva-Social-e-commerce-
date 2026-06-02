<?php

namespace Modules\Products\Traits\ECommerce;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\OrderDetails;
use Modules\Products\Repositories\ECommerce\ECommerceProductRepository;
use Modules\User\Entities\User;

trait NewAlgorithmProductRepositoryTrait
{

    public function groupedProducts()
    {
        /**
         *
            SELECT *
            FROM `products`
            LEFT JOIN `influencer_shop_product` ON `products`.`id` = `influencer_shop_product`.`product_id`
            WHERE `products`.`id` IN (
                SELECT * FROM (
                    SELECT `products`.`id`
                    FROM `products`
                    LEFT JOIN `influencer_shop_product` ON `products`.`id` = `influencer_shop_product`.`product_id`
                    WHERE `products`.`deleted_at` IS null and `influencer_shop_product`.`deleted_at` IS null
                        GROUP BY `products`.`id`
                        LIMIT 10
                        ) AS tyvan
            ) AND `influencer_shop_product`.`deleted_at` IS null;

         */
        return  DB::table('products')
            ->select('products.id')
            ->leftJoin('influencer_shop_product', 'products.id', '=', 'influencer_shop_product.product_id')
            ->leftJoin('users as influencer_users', 'influencer_users.id', '=', 'influencer_shop_product.influencer_id')
            ->leftJoin('users as business_users', 'business_users.id', '=', 'products.user_id')
            ->leftJoin('sub_categories', 'sub_categories.id', '=', 'products.sub_category')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->whereNull('products.deleted_at')
            ->whereNull('influencer_shop_product.deleted_at')
            ->whereNotNull('products.user_id')
            ->whereNotNull('influencer_shop_product.influencer_id')
            ->where('products.status', 1)
            ->where('products.stock', '>', 0)
            ->where('influencer_shop_product.status', 1)
            ->whereNotNull('influencer_users.id')
            ->whereNotNull('business_users.id')
            ->whereNull('influencer_users.deleted_at')
            ->whereNull('business_users.deleted_at')
            ->groupBy('products.id');
        // ->inRandomOrder();
        // ->orderByDesc('products.id');
    }

    public function getQueryProducts($filter)
    {
        return DB::table('products')
            ->leftJoin('influencer_shop_product', 'products.id', '=', 'influencer_shop_product.product_id')
            ->leftJoin('users as influencer_users', 'influencer_users.id', '=', 'influencer_shop_product.influencer_id')
            ->leftJoin('users as business_users', 'business_users.id', '=', 'products.user_id')
            ->leftJoin('sub_categories', 'sub_categories.id', '=', 'products.sub_category')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->whereNull('products.deleted_at')
            ->whereNull('influencer_shop_product.deleted_at')
            ->whereNotNull('products.user_id')
            ->whereNotNull('influencer_shop_product.influencer_id')
            ->where('products.status', 1)
            ->where('products.stock', '>', 0)
            ->where('influencer_shop_product.status', 1)
            ->whereNotNull('influencer_users.id')
            ->whereNotNull('business_users.id')
            ->whereNull('influencer_users.deleted_at')
            ->whereNull('business_users.deleted_at')
            ->select(
                'products.id as product_id',
                'products.user_id as product_user_id',
                // 'products.comp_id as product_comp_id',
                'products.category as product_category',
                'products.sub_category as product_sub_category',
                'products.name as product_name',
                // 'products.title as product_title',
                'products.sku as product_sku',
                'products.brand as product_brand',
                'products.rating as product_rating',
                'products.total_sale as product_total_sale',
                'products.created_by as product_created_by',
                'products.status as product_status',
                'products.description as product_description',
                'products.specification as product_specification',
                'products.color as product_color',
                'products.size as product_size',
                'products.stock as product_stock',
                'products.price as product_price',
                'products.retail_price as product_retail_price',
                // 'products.main_image as product_main_image',
                'products.discount as product_discount',
                // 'products.created_at as product_created_at',
                // 'products.updated_at as product_updated_at',
                // 'products.deleted_at as product_deleted_at',
                'products.influid as product_influid',
                'products.invite as product_invite',
                'products.has_shipping_fees as product_has_shipping_fees',
                'products.shipping_by as product_shipping_by',
                'products.views as product_views',
                'products.is_ali_express as product_is_ali_express',
                'influencer_shop_product.id as influencer_shop_product_id',
                'influencer_shop_product.influencer_id as influencer_shop_product_influencer_id',
                'influencer_shop_product.shop_id as influencer_shop_product_shop_id',
                'influencer_shop_product.product_id as influencer_shop_product_product_id',
                'influencer_shop_product.total_sale as influencer_shop_product_total_sale',
                'influencer_shop_product.product_cover_image as influencer_shop_product_product_cover_image',
                'influencer_shop_product.product_cover_image as influencer_shop_product_product_cover_image',
                'influencer_shop_product.product_title as influencer_shop_product_product_title',
                'influencer_shop_product.product_price as influencer_shop_product_product_price',
                'influencer_shop_product.product_description as influencer_shop_product_product_description',
                'influencer_shop_product.status as influencer_shop_product_status',
                // 'influencer_shop_product.created_at as influencer_shop_product_created_at',
                // 'influencer_shop_product.updated_at as influencer_shop_product_updated_at',
                // 'influencer_shop_product.deleted_at as influencer_shop_product_deleted_at',
                'influencer_shop_product.views as influencer_shop_product_views',
                'influencer_users.id as user_id',
                'influencer_users.name as user_name',
                'influencer_users.profile_image as profile_image',
                // 'users.privilege as user_privilege',
            )
            ->addSelect(DB::raw('(influencer_users.privilege + 1) as user_privilege'))
            // (SELECT JSON_ARRAYAGG(JSON_OBJECT('id', colors.id, 'name', colors.name, 'hexaval', colors.hexaval))
            //  FROM colors
            //  WHERE FIND_IN_SET(colors.id, products.color)) AS colors,
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', colors.id, 'name', colors.name, 'hexaval', colors.hexaval)) FROM colors WHERE FIND_IN_SET(colors.id, products.color)) AS color_details")
            )
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', sizes.id, 'name', sizes.name)) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) AS size_details")
            )
            ->addSelect(
                DB::raw("(SELECT image FROM product_image WHERE product_image.product_id = influencer_shop_product.product_id AND is_cover_pic = 1 limit 1) AS product_main_image")
            )
            ->addSelect(
                DB::raw("(SELECT image_path FROM influencer_shop_product_images WHERE influencer_shop_product_images.shop_product_id = influencer_shop_product.id AND is_cover_pic = 1) AS main_image")
            )
            ->addSelect(
                DB::raw("
            CASE
                WHEN discount IS NOT NULL THEN
                    ROUND(100 - ((discount * 100) / retail_price), 2)
                 ELSE NULL
            END AS discount_rate")
            )->addSelect('influencer_users.deleted_at as users_deleted_at')
            ->addSelect('business_users.deleted_at as business_users_deleted_at');
    }


    public function fetchProductByAlgorithm($products_selected, $products_query, $filter, $with_count = true)
    {

        $products_query_clone = $products_query->clone();

        if ($with_count) {
            $products_count = $products_query_clone->whereIn('products.id', ($products_selected->clone())->pluck('products.id'))->select('products.id', DB::raw('COUNT(products.id)'))->groupBy('products.id')
                ->get()
                ->count();
        } else {
            $products_count = 0;
        }


        // dd(($filter['page'] - 1) * $filter['per_page'], $filter['per_page']);
        $products_selected->limit($filter['per_page'])
            ->offset(($filter['page'] - 1) * $filter['per_page']); // for pages

        $products_query->whereIn('products.id', $products_selected->pluck('products.id'));


        $products = $products_query->get();
        $grouped_products = $products->groupBy('product_id');

        $avg = $grouped_products->map(function ($map) {
            return $map->avg('user_privilege');
        }); // ->avg('user_privilege');

        // dd($grouped_products);
        $sum_avg_products = $grouped_products->map(function ($product, $key) use ($avg) {
            return $product->sum('user_privilege');
        });



        /*
ali	1
hassan	1
reza	3
rand=0~5= 0
0:ali
1:hassan
2:hasan
3:reza
4:reza
5:reza
base=3
foreach(){
	if (rand<user.score+ base ) {
		//select user an return
	}else{
		base =base +user.score
	}
}
        */
        $map_products = $grouped_products->map(function ($products, $key) use ($sum_avg_products) {
            $rand = mt_rand(0, $sum_avg_products[$key] - 1);
            $base = 0;
            foreach ($products as $influencer_product) {
                if ($rand < $influencer_product->user_privilege + $base) {
                    return $influencer_product;
                } else {
                    $base += $influencer_product->user_privilege;
                }
            }
        })->values();

        return [$map_products, $products_count];
    }

    public function productsNewAlgorithm($filter)
    {


        $products_selected = $this->groupedProducts();
        $products_selected->inRandomOrder();

        // dd($products_selected->pluck('products.id'));
        // dd($products_selected);
        $products_query = $this->getQueryProducts($filter);
        // ->orderBy('users.privilege', 'desc');

        // start filter query
        if (array_key_exists('user_id', $filter) and $filter['user_id']) {
            $products_query->where('user_id', $filter['user_id']);
            $products_selected->where('user_id', $filter['user_id']);
        }
        if (array_key_exists('category_id', $filter) and $filter['category_id']) {
            $products_query->where('categories.id', 'like', $filter['category_id']);
            $products_selected->where('categories.id', 'like', $filter['category_id']);
        }
        if (array_key_exists('subcategory_id', $filter) and $filter['subcategory_id']) {
            $products_query->where('sub_categories.id', 'like', $filter['subcategory_id']);
            $products_selected->where('sub_categories.id', 'like', $filter['subcategory_id']);
        }
        if (array_key_exists('query', $filter) and $filter['query']) {
            $products_query->where(function ($where) use ($filter) {
                $where->where(function ($where_1) use ($filter) {
                    $where_1
                        ->where('influencer_shop_product.id', $filter['query'])
                        ->orWhere('products.id', $filter['query'])
                        ->orWhere('products.name', 'like', "%{$filter['query']}%")
                        ->orWhere('products.brand', 'like', "%{$filter['query']}%")
                        ->orWhere('products.sku', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_shop_product.product_title', 'like', "%{$filter['query']}%")
                        ->orWhere('sub_categories.name', 'like', "%{$filter['query']}%")
                        ->orWhere('categories.name', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.name', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.email', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.phone_number', 'like', "%{$filter['query']}%");
                });
            });
            $products_selected->where(function ($where) use ($filter) {
                $where->where(function ($where_1) use ($filter) {
                    $where_1
                        ->where('influencer_shop_product.id', $filter['query'])
                        ->orWhere('products.id', $filter['query'])
                        ->orWhere('products.name', 'like', "%{$filter['query']}%")
                        ->orWhere('products.brand', 'like', "%{$filter['query']}%")
                        ->orWhere('products.sku', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_shop_product.product_title', 'like', "%{$filter['query']}%")
                        ->orWhere('sub_categories.name', 'like', "%{$filter['query']}%")
                        ->orWhere('categories.name', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.name', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.email', 'like', "%{$filter['query']}%")
                        ->orWhere('influencer_users.phone_number', 'like', "%{$filter['query']}%");
                });
            });
        }
        if (array_key_exists('deals', $filter) and $filter['deals']) {
            if ($filter['deals'] == 'discounted') {
                $products_query->whereNotNull('products.discount');
                $products_selected->whereNotNull('products.discount');
            }
            if ($filter['deals'] == 'full_price' or $filter['deals'] == 'full-price') {
                $products_query->whereNull('products.discount');
                $products_selected->whereNull('products.discount');
            }
        }

        $filter = (new ECommerceProductRepository())->checkMinMaxPrice($filter);
        if (array_key_exists('min_price', $filter) and array_key_exists('max_price', $filter)) {
            $products_query->whereBetween('products.retail_price', [$filter['min_price'], $filter['max_price']]);
            $products_selected->whereBetween('products.retail_price', [$filter['min_price'], $filter['max_price']]);
        } elseif (array_key_exists('min_price', $filter)) {
            $products_query->where('products.retail_price', '>=', $filter['min_price']);
            $products_selected->where('products.retail_price', '>=', $filter['min_price']);
        } elseif (array_key_exists('max_price', $filter)) {
            $products_query->where('products.retail_price', '<=', $filter['max_price']);
            $products_selected->where('products.retail_price', '<=', $filter['max_price']);
        }
        if (array_key_exists('rating', $filter)) {
            $filter['rating'] = (int)$filter['rating'];
            $products_query->where('products.rating', '>=', $filter['rating']);
            $products_selected->where('products.rating', '>=', $filter['rating']);
        }
        // end filter query

        $products_query->inRandomOrder();
        // $products_query->orderByDesc('products.id');
        [$map_products, $products_count] = $this->fetchProductByAlgorithm($products_selected, $products_query, $filter);
        return [$map_products, $products_count];
    }

    public function hotDealsNewAlgorithm($filter)
    {


        $products_selected = $this->groupedProducts();

        $products_selected->whereRaw("
            CASE
                WHEN discount IS NOT NULL THEN ROUND(100 - ((discount * 100) / retail_price), 2) > 0
                ELSE 0
            END
        ");

        $products_selected->orderByRaw('CASE
                WHEN discount IS NOT NULL THEN
                    ROUND(100 - ((discount * 100) / retail_price), 2)
                 ELSE NULL
            END DESC');
        // dd($products_selected->pluck('products.id'));
        // dd($products_selected);
        $products_query = $this->getQueryProducts($filter);
        // ->orderBy('users.privilege', 'desc');

        $products_query->whereRaw("
            CASE
                WHEN discount IS NOT NULL THEN ROUND(100 - ((discount * 100) / retail_price), 2) > 0
                 ELSE 0
            END
        ");
        // ELSE 1 = 1 -- This can be used as a default case, it essentially means no filtering for the else part
        // ELSE 0 -- This ensures no rows are returned if none of the conditions match

        $products_query->orderByRaw('discount_rate DESC');

        [$map_products, $products_count] = $this->fetchProductByAlgorithm($products_selected, $products_query, $filter, false);
        return [$map_products, $products_count];
    }

    public function trendingNewAlgorithm($filter)
    {
        $products_selected = $this->groupedProducts();

        $products_selected->orderByRaw('products.is_trending DESC, products.id');


        $products_query = $this->getQueryProducts($filter);
        $products_query->orderByRaw('products.is_trending DESC, products.id');

        // start calculate frequencies of products
        $frequencies_sub_query = InfluencerShopProducts::select('product_id', DB::raw("COUNT(product_id) AS frequency"))
            ->groupBy('product_id');

        $products_query->leftJoinSub($frequencies_sub_query, 'frequencies', function ($join) {
            $join->on('influencer_shop_product.product_id', '=', 'frequencies.product_id');
        })
            ->addSelect('frequency');
        // end calculate frequencies of products

        // start calculate count of orders
        $orders_count_sub_query = OrderDetails::leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('shops', 'shops.id', '=', 'orders.shop_id')
            ->leftJoin('users', 'users.id', '=', 'shops.user_id')
            ->select('order_details.product_id', DB::raw("COUNT(order_details.product_id) AS orders_count"))
            ->where('orders.order_status', 3)
            ->whereBetween('orders.updated_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->where('users.type', User::InfluencerType)
            ->groupBy('order_details.product_id');

        $products_query->leftJoinSub($orders_count_sub_query, 'orders_count', function ($join) {
            $join->on('influencer_shop_product.id', '=', 'orders_count.product_id');
        })
            ->addSelect(DB::raw("IFNULL(orders_count.orders_count, 0) AS orders_count"));
        // end calculate count of orders

        $products_query->addSelect(DB::raw("(IFNULL(orders_count.orders_count, 0)*3 + frequency*2 + influencer_shop_product.views) AS ordered_by"));
        $products_query->orderByRaw('products.is_trending DESC ,(IFNULL(orders_count.orders_count, 0)*3 + frequency*2 + influencer_shop_product.views) DESC');

        // $products_query->dd();
        [$map_products, $products_count] = $this->fetchProductByAlgorithm($products_selected, $products_query, $filter, false);
        return [$map_products, $products_count];
    }
}

<?php


namespace Modules\Products\Repositories\ECommerce;

use Illuminate\Support\Facades\DB;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ECommerceCategoryRepository
{


    public function featured()
    {
        $categories_suggestions = $this->getFeaturedCategories();

        return $categories_suggestions;
    }

    public function getFeaturedCategories()
    {
        $filter = checkSubdomainAccount([]);

        if (array_key_exists('user_id', $filter) and $filter['user_id']) {
            if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType) {
                $categories_ids = Product::whereIn(
                    'id',
                    InfluencerShopProducts::where('influencer_id', $filter['user_id'])->select('product_id')->pluck('product_id')
                )
                    ->active()
                    ->select('category', 'sub_category')->get();
            }
            if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType) {
                $categories_ids = Product::where('user_id', $filter['user_id'])
                    ->active()
                    ->select('category', 'sub_category')->get();
            }
        }

        $featured_categories_query = Category::where('id', '>', 8)
            ->select(['id', 'name', 'is_active', 'is_featured'])
            ->withCount(['products', 'subcategories'])
            ->active()
            ->featured();

        if (array_key_exists('user_id', $filter) and $filter['user_id']) {
            if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType) {
                $featured_categories_query->whereIn('id', $categories_ids->pluck('category.id'));
            }
            if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType) {
                $featured_categories_query->whereIn('id', $categories_ids->pluck('category.id'));
            }
        }


        $featured_categories =    $featured_categories_query->get();

        return $featured_categories;
    }

    public function suggestions($filter)
    {
        $categories_suggestions = $this->getCategoriesSuggestions($filter);

        return $categories_suggestions;
    }

    public function getCategoriesSuggestions($filter)
    {

        $query = Category::where('id', '>', 8)
            ->select(['id', 'name', 'is_active'])
            ->withCount(['products', 'subcategories'])
            ->active();

        $query = $this->filterQuery($query, $filter);

        $categories_suggestions = $query->get();
        return $categories_suggestions;
    }
    public function filterQuery($query, $filter)
    {
        if (array_key_exists('query', $filter) and $filter['query']) {
            $query->with(['subcategories' => function ($with) use ($filter) {
                $with->filterByName($filter['query']);
            }])
                ->where(function ($where) use ($filter) {
                    $where->orWhere('name', 'like', "%{$filter['query']}%")
                        ->orWhereHas('subcategories', function ($with) use ($filter) {
                            $with->filterByName($filter['query']);
                        });
                });
            // ->orWhereHas('subcategories', function ($or_where_has) use ($filter) {
            //     $or_where_has->where('name', 'like', "%{$filter['query']}%")
            //         ->active();
            // });
        } else {
            $query->with(['subcategories' => function ($with) use ($filter) {
                $with->select([
                    'id',
                    'category_id',
                    'name',
                    'is_color',
                    'is_size',
                    'is_active'
                ])
                    ->withCount('products')
                    ->active();
            }]);
        }
        return  $query;
    }

    public function getSubCategories($filter)
    {
        $filter = checkSubdomainAccount($filter);
        $cache_key = __FUNCTION__ . md5(json_encode($filter));
        return cache()->remember($cache_key, 60 * config('twiva.e-commerce.cache_time_per_minutes'), function () use ($filter) {
            if (array_key_exists('user_id', $filter) and $filter['user_id']) {
                if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType) {
                    $categories_ids = Product::whereIn(
                        'id',
                        InfluencerShopProducts::where('influencer_id', $filter['user_id'])->select('product_id')->pluck('product_id')
                    )
                        ->active()
                        ->select('category', 'sub_category')->get();
                }
                if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType) {
                    $categories_ids = Product::where('user_id', $filter['user_id'])
                        ->active()
                        ->select('category', 'sub_category')->get();
                }
            }


            $product_min_max_sub_query = Product::select(
                'category as category_id',
                DB::raw('MIN(CAST(retail_price AS DECIMAL(10, 2))) as min_price'),
                DB::raw('MAX(CAST(retail_price AS DECIMAL(10, 2))) as max_price')
            )
                ->groupBy('category')
                ->active();
            // ->get();
            // dd($product_min_max_sub_query);

            $categories_query = Category::where('id', '>', 8)
                ->select(['id', 'name', 'is_active', 'product_min_max.min_price', 'product_min_max.max_price'])
                ->withCount([
                    'products' => function ($products) {
                        return $products->active()->where('stock', '>', 0);
                    },
                    'subcategories'
                ])
                ->leftJoinSub($product_min_max_sub_query, 'product_min_max', function ($left_join_sub) {
                    return $left_join_sub->on('categories.id', '=', 'product_min_max.category_id');
                })
                ->having('products_count', '>', 0)
                ->active();

            if (array_key_exists('category_id', $filter) and $filter['category_id']) {
                $categories_query->where('id', $filter['category_id']);
            }

            if (array_key_exists('subcategory_id', $filter) and $filter['subcategory_id']) {
                $categories_query->whereHas('subcategories', function ($where_has) use ($filter) {
                    $where_has->where('id', $filter['subcategory_id']);
                });
            }

            if (array_key_exists('query', $filter) and $filter['query']) {
                $categories_query->where(function ($where) use ($filter) {
                    $where->whereHas('products', function ($where_has) use ($filter) {
                        $where_has->where(function ($where) use ($filter) {
                            $where->where('id', $filter['query'])
                                ->orWhere('brand', 'like', "%{$filter['query']}%")
                                ->orWhere('name', 'like', "%{$filter['query']}%")
                                ->orWhere('sku', 'like', "%{$filter['query']}%");
                        })->orWhereHas('user', function ($or_where_has) use ($filter) {
                            $or_where_has->where('name', 'like', "%{$filter['query']}%");
                        });
                    })->orWhereHas('products.eShopConnected', function ($or_where_has) use ($filter) {
                        $or_where_has->where('id', $filter['query'])
                            ->orWhere('product_title', 'like', "%{$filter['query']}%")
                            ->orWhereHas('user', function ($or_where_has) use ($filter) {
                                $or_where_has->where('name', 'like', "%{$filter['query']}%");
                            });
                    });
                });
            }

            if (array_key_exists('user_id', $filter) and $filter['user_id']) {
                $categories_query->whereHas('subcategories', function ($where_has) use ($categories_ids) {
                    $where_has->whereIn('id', $categories_ids->pluck('sub_category.id'));
                });
                $categories_query->with(['subcategories'  => function ($with) use ($categories_ids) {
                    $with->select([
                        'id',
                        'category_id',
                        'name',
                        'is_active'
                    ])
                        ->withCount([
                            'products'  => function ($products) {
                                return $products->active()->where('stock', '>', 0)->whereNull('deleted_at');
                            }
                        ])
                        ->whereIn('id', $categories_ids->pluck('sub_category.id'))
                        ->having('products_count', '>', 0)
                        ->active();
                }]);
            } else {
                $categories_query->with(['subcategories'  => function ($with) use ($filter) {
                    $with->select([
                        'id',
                        'category_id',
                        'name',
                        'is_active'
                    ])
                        ->withCount([
                            'products'  => function ($products) {
                                return $products->active()->where('stock', '>', 0)->whereNull('deleted_at');
                            }
                        ])
                        ->having('products_count', '>', 0)
                        ->active();
                }]);
            }

            $categories = $categories_query->get();


            $min_price = $categories->min('min_price');
            $max_price = $categories->max('max_price');

            return ['categories' => $categories, 'min' => $min_price, 'max' => $max_price];
        });
    }
}

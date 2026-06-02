<?php


namespace Modules\Products\Repositories\ECommerce;

use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;
use Modules\Products\Traits\ECommerce\NewAlgorithmProductRepositoryTrait;
use Modules\User\Entities\User;

class ECommerceProductRepository {
    use NewAlgorithmProductRepositoryTrait;

    // start hot deals section
    public function productsHotDeals($filter)
    {
        $business_products = $this->getBusinessHotDealProducts($filter);
        $influencer_products = $this->getInfluencerHotDealProducts($filter);

        return $business_products->merge($influencer_products);
    }

    public function getBusinessHotDealProducts($filter)
    {
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType)
        {
            return collect();
        }
        else
        {
            if (array_key_exists('user_id', $filter) and $filter['user_id'])
            {
            }
            else
            {
                return collect();
            }
        }
        $query = Product::basicSelect()
            ->active()
            ->whereHas('user')
            ->whereNotNull('discount')
            ->withDiscountPercentage()
            ->with(['user:id,name,type,profile_image', 'user.shop:id,user_id,shop_name,domain'])
            ->where('stock', '>', 0);

        if (array_key_exists('user_id', $filter) and $filter['user_id'])
        {
            $query->where('user_id', $filter['user_id']);
        }


        $business_products = $query->paginate($filter['per_page'])
            ->append(['product_price', 'discount_rate']);
        return $business_products;
    }

    public function getInfluencerHotDealProducts($filter)
    {
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType)
        {
            return collect();
        }
        $query = InfluencerShopProducts::select([
            'influencer_shop_product.id',
            'influencer_shop_product.product_title as name',
            'influencer_shop_product.influencer_id',
            'influencer_shop_product.influencer_id as user_id',
            'influencer_shop_product.product_price',
            'influencer_shop_product.product_id'
        ])
            ->withDiscountPercentage()
            ->with([
                'user:id,name,type,profile_image',
                'user.shop:id,user_id,shop_name,domain',
                'actualDetail' => function ($actualDetail)
                {
                    $actualDetail->where('stock', '>', 0)->whereNotNull('discount')->basicSelect()->withDiscountPercentage();
                }
            ])
            ->active()
            ->whereHas('user')
            ->whereHas('actualDetail')
            ->whereHas('actualDetail.user')
            ->whereHas('actualDetail', function ($whereHas)
            {
                $whereHas->active()->where('stock', '>', 0)->whereNotNull('discount');
            })
            ->leftJoin('products', 'products.id', '=', 'influencer_shop_product.product_id');

        if (array_key_exists('user_id', $filter) and $filter['user_id'])
        {
            $query->where('influencer_id', $filter['user_id']);
        }

        $influencer_products = $query->paginate($filter['per_page'])
            ->append(['main_image_path', 'brand', 'retail_price', 'discount', 'discount_rate', 'sku']);

        return $influencer_products;
    }
    // end hot deals section
    ////////////////////////////////////////////////////////////////////////////
    // start products section
    public function products($filter)
    {
        [$business_products, $products_counts, $min_max_price_products, $product_last_page, $sub_categories_business] = $this->getBusinessProducts($filter);
        [$influencer_products, $influencer_products_counts, $min_max_price_influencer_products, $influencer_product_last_page, $sub_categories_influencer] = $this->getInfluencerProducts($filter);

        $sub_categories = $sub_categories_business->merge($sub_categories_influencer);
        $min_max_price = [
            'min' => min($min_max_price_products['min'], $min_max_price_influencer_products['min']),
            'max' => max($min_max_price_products['max'], $min_max_price_influencer_products['max']),
        ];

        return [$business_products->merge($influencer_products)->sortByDesc('privilege')->shuffle(), $products_counts, $influencer_products_counts, $min_max_price, $product_last_page, $influencer_product_last_page, $sub_categories];
    }

    public function getBusinessProducts($filter)
    {
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType)
        {
            return [collect(), 0, ['min' => 0, 'max' => 0], 1, collect()];
        }
        else
        {
            if (array_key_exists('user_id', $filter) and $filter['user_id'])
            {
            }
            else
            {
                return [collect(), 0, ['min' => 0, 'max' => 0], 1, collect()];
            }
        }
        $query = Product::basicSelect()
            ->with('eventTicketProduct')
            ->whereHas('user')
            ->active()
            ->with(['user:id,name,type,profile_image,privilege', 'user.shop:id,user_id,shop_name,domain','eventTicketProduct:id,business_product_id'])
            ->where('stock', '>', 0);

        if (array_key_exists('user_id', $filter) and $filter['user_id'])
        {
            $query->where('user_id', $filter['user_id']);
        }

        $query = $this->filterQueryProducts($query, $filter);
        $query = $this->addOrderToQuery($query, $filter, User::BusinessType);

        $counts = $this->getCountOfQuery($query);
        $min_max_price = $this->getMinMaxPriceFromQuery($query, 'retail_price');
        $sub_categories = $this->getSubcategoriesFromQuery($query);
        if (($filter['product_type'] ?? false) == 'services')
        {
            $query = $query->whereHas('eventTicketProduct');
        }
        else
        {
            $query = $query->whereDoesntHave('eventTicketProduct');
        }
        $query = $query->orderByDesc(User::select('privilege')->whereColumn('users.id', 'products.user_id'));

        // $query->dd();
        $business_products = $query->paginate($filter['per_page']);
        $last_page = $business_products->lastPage();
        $business_products = $business_products->append(['product_price', 'discount_rate']);
        return [$business_products, $counts, $min_max_price, $last_page, $sub_categories];
    }

    public function filterQueryProducts($query, $filter)
    {
        $query = $this->addWhereHasFilterFor($query, $filter, 'category_id', 'category');
        $query = $this->addWhereHasFilterFor($query, $filter, 'subcategory_id', 'subCategory');

        if (array_key_exists('query', $filter) and $filter['query'])
        {
            $query->where(function ($where) use ($filter)
            {
                $where->where(function ($where_1) use ($filter)
                {
                    $where_1->where('id', $filter['query'])
                        ->orWhere('brand', 'like', "%{$filter['query']}%")
                        ->orWhere('name', 'like', "%{$filter['query']}%")
                        ->orWhere('sku', 'like', "%{$filter['query']}%");
                })->orWhere(function ($or_where) use ($filter)
                {
                    $or_where->whereHas('category', function ($category) use ($filter)
                    {
                        $category->where('name', 'like', "%{$filter['query']}%");
                    })
                        ->orWhereHas('subCategory', function ($sub_category) use ($filter)
                        {
                            $sub_category->where('name', 'like', "%{$filter['query']}%");
                        })
                        ->orWhereHas('user', function ($user) use ($filter)
                        {
                            $user->where(function ($user_where) use ($filter)
                            {
                                $user_where->where('name', 'like', "%{$filter['query']}%")
                                    ->orWhere('email', 'like', "%{$filter['query']}%")
                                    ->orWhere('phone_number', 'like', "%{$filter['query']}%");
                            });
                        });
                });
            });
        }

        $query = $this->addDealsFilterFor($query, $filter, User::BusinessType);
        $query = $this->addBetweenPriceFilterFor($query, $filter, User::BusinessType);
        $query = $this->addRatingFilterFor($query, $filter, User::BusinessType);

        return $query;
    }

    public function getInfluencerProducts($filter)
    {
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType)
        {
            return [collect(), 0, ['min' => 0, 'max' => 0], 1, collect()];
        }

        $query = InfluencerShopProducts::select([
            'id',
            'product_title as name',
            'influencer_id',
            'influencer_id as user_id',
            'product_price',
            'product_id'
        ])
            ->with([
                'user:id,name,type,profile_image,privilege',
                'user.shop:id,user_id,shop_name,domain',
                'actualDetail' => function ($actualDetail)
                {
                    $actualDetail->with('eventTicketProduct')->basicSelect();
                }
            ])
            ->whereHas('user')
            ->whereHas('actualDetail')
            ->whereHas('actualDetail.user')
            ->active()
            ->whereHas('actualDetail', function ($whereHas)
            {
                $whereHas->active()->basicSelect()->where('stock', '>', 0);
            });

        if (array_key_exists('user_id', $filter) and $filter['user_id'])
        {
            $query->where('influencer_id', $filter['user_id']);
        }

        $query = $this->filterQueryInfluencerProducts($query, $filter);
        $sub_categories = $this->getSubcategoriesFromQuery($query);

        $query = $this->addOrderToQuery($query, $filter, User::InfluencerType);
        $query = $query->orderByDesc(User::select('privilege')->whereColumn('users.id', 'influencer_shop_product.influencer_id'));

        if (($filter['product_type'] ?? false) == 'services')
        {
            $query = $query->whereHas('actualDetail.eventTicketProduct');
        }
        else
        {
            $query = $query->whereDoesntHave('actualDetail.eventTicketProduct');
        }
        // $query->dd();
        $counts = $this->getCountOfQuery($query);
        $min_max_price = $this->getMinMaxPriceFromQuery($query, 'product_price');


        // $categories = $this->fetchRelatedCategories($query);

        $influencer_products = $query->paginate($filter['per_page']);

        $last_page = $influencer_products->lastPage();

        $influencer_products = $influencer_products->append(['main_image_path', 'brand', 'retail_price', 'discount', 'discount_rate', 'sku']);

        return [$influencer_products, $counts, $min_max_price, $last_page, $sub_categories];
    }

    public function filterQueryInfluencerProducts($query, $filter)
    {
        $query = $this->addWhereHasFilterFor($query, $filter, 'category_id', 'product.category');
        $query = $this->addWhereHasFilterFor($query, $filter, 'subcategory_id', 'product.subCategory');

        if (array_key_exists('query', $filter) and $filter['query'])
        {
            $query->where(function ($where) use ($filter)
            {
                $where->where(function ($where_1) use ($filter)
                {
                    $where_1->where('influencer_shop_product.id', $filter['query'])
                        ->orWhere('influencer_shop_product.product_title', 'like', "%{$filter['query']}%");
                })->orWhere(function ($or_where) use ($filter)
                {
                    $or_where->whereHas('product.category', function ($category) use ($filter)
                    {
                        $category->where('name', 'like', "%{$filter['query']}%");
                    })
                        ->orWhereHas('product.subCategory', function ($sub_category) use ($filter)
                        {
                            $sub_category->where('name', 'like', "%{$filter['query']}%");
                        })
                        ->orWhereHas('user', function ($user) use ($filter)
                        {
                            $user->where(function ($user_where) use ($filter)
                            {
                                $user_where->where('name', 'like', "%{$filter['query']}%")
                                    ->orWhere('email', 'like', "%{$filter['query']}%")
                                    ->orWhere('phone_number', 'like', "%{$filter['query']}%");
                            });
                        })
                        ->orWhereHas('product', function ($products) use ($filter)
                        {
                            $products->where(function ($where_1) use ($filter)
                            {
                                $where_1->where('id', $filter['query'])
                                    ->orWhere('brand', 'like', "%{$filter['query']}%")
                                    ->orWhere('name', 'like', "%{$filter['query']}%")
                                    ->orWhere('sku', 'like', "%{$filter['query']}%");
                            });
                        });
                });
            });
        }

        $query = $this->addDealsFilterFor($query, $filter, User::InfluencerType);
        $query = $this->addBetweenPriceFilterFor($query, $filter, User::InfluencerType);
        $query = $this->addRatingFilterFor($query, $filter, User::InfluencerType);

        return $query;
    }

    public function fetchRelatedCategories($query)
    {
        $product_query = $query->clone();
        dd($product_query);
    }

    public function checkSortByFieldName($filter, $type = User::BusinessType)
    {
        if ($type == User::BusinessType)
        {
        }
        else
        {
            $filter['sort_by'] = $filter['sort_by'] == 'retail_price' ? 'product_price' : $filter['sort_by'];
        }
        return $filter;
    }

    public function getCountOfQuery($query)
    {
        return ($query->clone())->count();
    }

    public function getMinMaxPriceFromQuery($query, $column)
    {
        $min = ($query->clone())->min($column);
        $max = ($query->clone())->max($column);
        if (!is_null(request('min_price')) || !is_null(request('max_price')))
        {
            $min = session('min_price');
            $max = session('max_price');
        }
        else
        {
            session(['min_price' => $min]);
            session(['max_price' => $max]);
        }
        $min_max_price = ['min' => $min, 'max' => $max];
        return $min_max_price;
    }

    public function addWhereHasFilterFor($query, $filter, $key, $name_of_relation)
    {
        if (array_key_exists($key, $filter) and $filter[$key])
        {
            $query->where(function ($where) use ($name_of_relation, $filter, $key)
            {
                $where->whereHas($name_of_relation, function ($category) use ($filter, $key)
                {
                    $category->where('id', $filter[$key]);
                });
            });
        }
        return $query;
    }

    public function addOrderToQuery($query, $filter, $type)
    {
        if (array_key_exists('sort_by', $filter) and $filter['sort_by'])
        {
            $filter = $this->checkSortByFieldName($filter, $type);
            $query->orderBy($filter['sort_by'], $filter['sort_direction']);
        }
        return $query;
    }

    public function addDealsFilterFor($query, $filter, $user_type)
    {
        if (array_key_exists('deals', $filter) and $filter['deals'])
        {
            $deals = $this->prepareDealsValue($filter['deals']);
            foreach ($deals as $key => $deal)
            {
                if ($deal == 'discounted')
                {
                    $query = $this->addDiscountedFilterToProducts($query, $user_type);
                }
                if ($deal == 'full_price' or $deal == 'full-price')
                {
                    $query = $this->addFullPriceFilterToProducts($query, $user_type);
                }
            }
        }
        return $query;
    }

    public function prepareDealsValue($deals)
    {
        return explode(',', $deals);
    }

    public function addDiscountedFilterToProducts($query, $user_type)
    {
        if ($user_type == User::BusinessType)
        {
            $query->whereNotNull('discount');
        }
        elseif ($user_type == User::InfluencerType)
        {
            $query->whereHas('product', function ($where_has)
            {
                $where_has->whereNotNull('discount');
            });
        }
        return $query;
    }

    public function addFullPriceFilterToProducts($query, $user_type)
    {
        if ($user_type == User::BusinessType)
        {
            $query->whereNull('discount');
        }
        elseif ($user_type == User::InfluencerType)
        {
            $query->whereHas('product', function ($where_has)
            {
                $where_has->whereNull('discount');
            });
        }
        return $query;
    }

    public function addBetweenPriceFilterFor($query, $filter, $user_type)
    {
        $filter = $this->checkMinMaxPrice($filter);
        if (array_key_exists('min_price', $filter) and array_key_exists('max_price', $filter))
        {
            if ($user_type == User::BusinessType)
            {
                $query->whereBetween('retail_price', [$filter['min_price'], $filter['max_price']]);
            }
            elseif ($user_type == User::InfluencerType)
            {
                $query->whereHas('product', function ($where_has) use ($filter)
                {
                    $where_has->whereBetween('retail_price', [$filter['min_price'], $filter['max_price']]);
                });
            }
        }
        elseif (array_key_exists('min_price', $filter))
        {
            if ($user_type == User::BusinessType)
            {
                $query->where('retail_price', '>=', $filter['min_price']);
            }
            elseif ($user_type == User::InfluencerType)
            {
                $query->whereHas('product', function ($where_has) use ($filter)
                {
                    $where_has->where('retail_price', '>=', $filter['min_price']);
                });
            }
        }
        elseif (array_key_exists('max_price', $filter))
        {
            if ($user_type == User::BusinessType)
            {
                $query->where('retail_price', '<=', $filter['max_price']);
            }
            elseif ($user_type == User::InfluencerType)
            {
                $query->whereHas('product', function ($where_has) use ($filter)
                {
                    $where_has->where('retail_price', '<=', $filter['max_price']);
                });
            }
        }
        return $query;
    }

    public function checkMinMaxPrice($filter)
    {
        if (array_key_exists('min_price', $filter) and array_key_exists('max_price', $filter))
        {
            $filter['max_price'] = (int)$filter['max_price'];
            $filter['min_price'] = (int)$filter['min_price'];
            if ($filter['min_price'] > $filter['max_price'])
            {
                list($filter['min_price'], $filter['max_price']) = [$filter['max_price'], $filter['min_price']];
            }
        }
        elseif (array_key_exists('min_price', $filter))
        {
            $filter['min_price'] = (int)$filter['min_price'];
        }
        elseif (array_key_exists('max_price', $filter))
        {
            $filter['max_price'] = (int)$filter['max_price'];
        }
        return $filter;
    }

    public function addRatingFilterFor($query, $filter, $user_type)
    {
        if (array_key_exists('rating', $filter))
        {
            $filter['rating'] = (int)$filter['rating'];
            if ($user_type == User::BusinessType)
            {
                $query->where('rating', '>=', $filter['rating']);
            }
            elseif ($user_type == User::InfluencerType)
            {
                $query->whereHas('product', function ($where_has) use ($filter)
                {
                    $where_has->where('rating', '>=', $filter['rating']);
                });
            }
        }
        return $query;
    }
    // end products section
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // start trending section
    public function productsTrending($filter)
    {
        $business_products = $this->getBusinessTrendingProducts($filter);
        $influencer_products = $this->getInfluencerTrendingProducts($filter);

        return $business_products->merge($influencer_products);
    }

    public function getBusinessTrendingProducts($filter)
    {
        return collect();
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::InfluencerType)
        {
            return collect();
        }
        $query = Product::basicSelect()
            ->with(['user:id,name,type,profile_image', 'user.shop:id,user_id,shop_name,domain'])
            ->active()
            ->whereHas('user')
            ->withCountOfRelatedInfluencerProducts(false)
            ->withCountOfOrders(false)
            ->orderByRaw('(orders_count*3 + frequency*2 + views) DESC');
        // ->orderBy('orders_count', 'DESC')
        // ->orderBy('frequency', 'DESC');
        $business_products = $query->paginate($filter['per_page'])
            ->append(['product_price', 'discount_rate']);
        return $business_products;
    }

    public function getInfluencerTrendingProducts($filter)
    {
        if (array_key_exists('user_type', $filter) and $filter['user_type'] == User::BusinessType)
        {
            return collect();
        }

        $query = InfluencerShopProducts::basicSelect()
            ->with(['user:id,name,type,profile_image', 'user.shop:id,user_id,shop_name,domain'])
            ->active()
            ->whereHas('user')
            ->whereHas('actualDetail.user')
            ->whereHas('actualDetail', function ($whereHas)
            {
                $whereHas->active()->basicSelect()->where('stock', '>', 0);
            })
            ->withCountOfRelatedInfluencerProducts(false)
            ->withCountOfOrders(false)
            ->orderByRaw('(orders_count*3 + frequency*2 + views) DESC');
        // ->orderBy('orders_count', 'DESC')
        // ->orderBy('frequency', 'DESC');
        $influencer_products = $query->paginate($filter['per_page'])
            ->append(['main_image_path', 'brand', 'retail_price', 'discount', 'discount_rate', 'sku']);

        return $influencer_products;
    }

    //end trending section


    public function getSubcategoriesFromQuery($query)
    {
        $new_query = $query->clone();

        if ($new_query->getModel()->getTable() == 'products')
        {
            $sub_categories = $new_query->select('products.sub_category')->groupBy('products.sub_category')->pluck('sub_category');
        }
        else
        {
            $sub_categories_ids = $new_query->select('products.sub_category')
                ->leftJoin('products', 'products.id', '=', 'influencer_shop_product.product_id')
                ->groupBy('products.sub_category')
                ->pluck('products.sub_category');

            $sub_categories = SubCategories::select(['id', 'category_id', 'name'])->whereIn('id', $sub_categories_ids)->get();
        }

        return $sub_categories;
    }
}

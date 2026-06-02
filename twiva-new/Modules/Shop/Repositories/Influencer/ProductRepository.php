<?php


namespace Modules\Shop\Repositories\Influencer;


use Illuminate\Support\Str;
use Modules\Influencer\Entities\InfluencerShopProducts;
use function request;

class ProductRepository {
    private $account;

    private $shopRepository;
    private $shopID;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->account = request('account');
        $this->shopRepository = $shopRepository;
        $this->shopID = $shopRepository->getShopID()->id ?? null;

    }

    public function getProductInformation($product)
    {
        return $this->productQuery()->where(['id' => $product, 'shop_id' => $this->shopID])->firstOrFail();
    }

    public function getProductInformationSortedBy()
    {
        $orderBy = [];
        switch (request('sortBy'))
        {
            case 'oldest' :
                $orderBy[] = 'created_at';
                $orderBy[] = 'ASC';
                break;
            case 'newest' :
                $orderBy[] = 'created_at';
                $orderBy[] = 'DESC';
                break;
            case 'lowest_price' :
                $orderBy[] = 'product_price';
                $orderBy[] = 'ASC';
                break;
            case 'highest_price' :
                $orderBy[] = 'product_price';
                $orderBy[] = 'DESC';
                break;
        }
        if (request('keyWord'))
        {
            return $this->productQuery()->where('shop_id', $this->shopID)
                ->where('product_title', 'LIKE', '%' . request('keyWord') . '%')
                ->orderBy($orderBy[0], $orderBy[1])->get();
        }
        return $this->productQuery()->where('shop_id', $this->shopID)->orderBy($orderBy[0], $orderBy[1])->get();
    }

    private function productQuery()
    {
        return InfluencerShopProducts::select('id', 'product_id', 'shop_id', 'product_title', 'product_price', 'product_description', 'created_at')
            ->with([
                'productMainImage' => function ($query)
                {
                    return $query->select('shop_product_id', 'image_path', 'is_cover_pic');
                },
                'actualDetail'     => function ($query)
                {
                    return $query->with('eventTicketProduct')
                        ->select('id', 'specification', 'color', 'size', 'price', 'retail_price', 'discount', 'brand', 'is_ali_express as product_is_ali_express');
                },

            ])
            ->whereHas('actualDetail')
            ->where('status', 1);
    }

    public function searchProducts()
    {

        $products = $this->productQuery()->where('shop_id', $this->shopID);
        if (request('keyWord'))
        {
            $products = $this->productQuery()->where('shop_id', $this->shopID)->where(function ($where)
            {
                $where->where('product_title', 'LIKE', '%' . request('keyWord') . '%')
                    ->orWhereHas('product', function ($where_has_product)
                    {
                        $where_has_product->where('brand', 'LIKE', '%' . request('keyWord') . '%');
                    });
            });
        }


        if (request('category_id') || request('category'))
        {
            $products->whereHas('product', function ($productDetails)
            {
                $productDetails->where('category', request('category_id') ?? request('category'));
            });
        }
        if (request('subcategory_id'))
        {
            $products->whereHas('product', function ($productDetails)
            {
                $productDetails->where('sub_category', request('subcategory_id'));
            });
        }
        if (request('rating') && request('rating') != 'all')
        {
            $products->whereHas('productReviewsAndRatings', function ($query)
            {
                $query->where('rating', '>=', request('rating'));
            });
        }
        if (request('max_price') && request('min_price'))
        {
            $products->whereBetween('product_price', [request('min_price'), request('max_price')]);
        }
        if (request('deals') && !Str::contains(request('deals'), 'all'))
        {
            if (Str::contains(request('deals'), 'discounted'))
            {
                $products->whereHas('product', function ($productDetails)
                {
                    $productDetails->whereNotNull('discount');
                });
            }
            else
            {
                $products->whereHas('product', function ($productDetails)
                {
                    $productDetails->whereNull('discount');
                });
            }
        }
        return $products->get();
    }

    public function getSimilarProducts()
    {
        return $this->shopRepository->getSimilarProducts();
    }

}

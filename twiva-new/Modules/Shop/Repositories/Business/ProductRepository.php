<?php


namespace Modules\Shop\Repositories\Business;


use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\InfluencerShopProducts;
use function request;

class ProductRepository
{
    private $account;

    private $shopRepository;
    private $shopID;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->account = request('account');
        $this->shopRepository = $shopRepository;
        $this->shopID = $shopRepository->getShop()->id ?? null;
        $this->shopUserID = $shopRepository->getShop()->user_id ?? null;

    }

    public function getProductInformation($product)
    {
        return $this->productQuery()->where(['id' => $product, 'user_id' => $this->shopUserID])->firstOrFail();
    }

    public function getProductInformationSortedBy()
    {
        $orderBy = [];
        switch (request('sortBy')) {
            case 'oldest' :
                $orderBy[] = 'created_at';
                $orderBy[] = 'ASC';
                break;
            case 'newest' :
                $orderBy[] = 'created_at';
                $orderBy[] = 'DESC';
                break;
            case 'lowest_price' :
                $orderBy[] = 'price';
                $orderBy[] = 'ASC';
                break;
            case 'highest_price' :
                $orderBy[] = 'price';
                $orderBy[] = 'DESC';
                break;
        }
        if (request('keyWord')) {
            return $this->productQuery()->where('user_id', $this->shopUserID)
                ->where('name', 'LIKE', '%' . request('keyWord') . '%')
                ->orderBy($orderBy[0], $orderBy[1])->get();
        }
        return $this->productQuery()
//            ->where('shop_id', $this->shopID)
            ->orderBy($orderBy[0], $orderBy[1])
            ->get();
    }

    private function productQuery()
    {
        return Product::select('id', 'name', 'price', 'retail_price', 'description', 'color', 'size', 'created_at', 'user_id', 'discount', 'status', 'brand', 'is_ali_express as product_is_ali_express')
            ->with('eventTicketProduct')
            ->where('status', 1);
    }

    public function searchProducts()
    {
        if (!request('keyWord')) {
            return $this->productQuery()->where('user_id', $this->shopUserID)->get();
        }
        $products =  $this->productQuery()->where('user_id', $this->shopUserID)
            ->where(function ($where) {
                $where->where('name', 'LIKE', '%' . request('keyWord') . '%')
                    ->orWhere('brand', 'LIKE', '%' . request('keyWord') . '%');
            })
            ->get();
        return $products;
    }

    public function getSimilarProducts()
    {
        return $this->shopRepository->getSimilarProducts();
    }

}

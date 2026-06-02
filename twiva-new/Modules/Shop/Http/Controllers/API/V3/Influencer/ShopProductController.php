<?php

namespace Modules\Shop\Http\Controllers\API\V3\Influencer;

use Illuminate\Routing\Controller;
use Modules\Products\Repositories\Influencer\ProductRepository;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Requests\Influencer\StoreShopProductRequest;
use Modules\Shop\Http\Requests\Influencer\UpdateShopProductRequest;
use Modules\Shop\Http\Resources\Catelog\ShowShopProductsResource;
use Modules\Shop\Http\Resources\Shop\Influencer\IndexProductsCollection;

class ShopProductController extends Controller
{

    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    public function index()
    {
        if ($this->getShopID())
        {
            $shopProducts = $this->productRepository->getShopProductsToIndex($this->getShopID(), request('keyword'),
                request('subcategories'), request('priceOrder'));
            return response()->json(['data' => IndexProductsCollection::collection($shopProducts)->resource]);
        }
        abort(403);
    }

    public function show()
    {
        if ($this->getShopID())
        {
            $shopProduct = $this->productRepository->showShopProduct($this->getShopID());
            return response()->json(['data' => new ShowShopProductsResource($shopProduct)]);
        }
        abort(403);
    }

    public function store(StoreShopProductRequest $request)
    {
        $this->shop_id = $this->getShopID();
        $data = $request->validated();
        $this->productRepository->storeShopProduct($this->getShopID(), $data);
        return response()->json(['data' => 'Product Saved Successfully']);
    }

    public function update(UpdateShopProductRequest $request)
    {
        $this->shop_id = $this->getShopID();
        $data = $request->validated();
        $this->productRepository->updateShopProduct($data);
        return response()->json(['data' => 'Product updated Successfully']);
    }

    public function deleteImage()
    {
        $this->productRepository->deleteShopProductImage($this->getShopID(), request('product_id'), request('image_id'));
        return response()->json(['data' => 'Product Image has been Deleted']);
    }

    public function deleteProduct()
    {
        $this->productRepository->deleteShopProduct($this->getShopID(), request('product_id'));
        return response()->json(['data' => 'Product Deleted Successfuly']);

    }


    private function getShopID()
    {
        return Shops::select('id', 'user_id')->where('user_id', auth('api')->id())->first()?->id ?? null;
    }

}

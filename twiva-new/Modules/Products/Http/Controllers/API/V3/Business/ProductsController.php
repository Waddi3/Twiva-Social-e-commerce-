<?php

namespace Modules\Products\Http\Controllers\API\V3\Business;

use Illuminate\Routing\Controller;
use Modules\Influencer\Entities\Product;
use Modules\Products\Http\Requests\Business\CreateProductRequest;
use Modules\Products\Http\Requests\Business\DeleteProductImageRequest;
use Modules\Products\Http\Requests\Business\DeleteProductRequest;
use Modules\Products\Http\Requests\Business\UpdateProductRequest;
use Modules\Products\Http\Resources\BusinessProductResource;
use Modules\Products\Http\Resources\CatelogIndexCollection;
use Modules\Products\Repositories\Business\ProductRepository;

class ProductsController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->getProductsToIndex(
            user_id: auth('api')->id(),
            keyword: request('keyword'),
            subcategories: request('subcategories'),
            is_published: request('is_published'),
            priceOrder: request('priceOrder')
        );
        return response()->json(['data' => CatelogIndexCollection::collection($products->paginate(15))->resource]);
    }

    public function show()
    {
        $product = $this->productRepository->getProductToShow(request('product_id'));
        return response()->json(['data' => new BusinessProductResource($product)]);
    }

    public function store(CreateProductRequest $request)
    {
        $date = $request->validated();
        $product = $this->productRepository->storeProduct($date);
        return response()->json(['data' => ['message' => "Product created successfully", 'product_id' => $product->id]]);
    }

    public function update(UpdateProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::where('id', $data['product_id'])->where('user_id', auth('api')->id())->firstOrFail();
        unset($data['product_id']);
        $product = $this->productRepository->updateProduct($product, $data);
        return response()->json(['data' => "Product $product updated successfully"]);
    }

    public function delete(DeleteProductRequest $request)
    {
        $data = $request->validated();
        $this->productRepository->deleteProduct($data['product_id']);
        return response()->json(['data' => 'Product Deleted']);
    }

    public function deleteImageFromProduct(DeleteProductImageRequest $request)
    {
        $data = $request->validated();
        $this->productRepository->deleteImageFromProduct($data['product_id'], $data['image_id']);
        return response()->json(['data' => 'Product Image Deleted']);
    }

    public function toggleProductStatus()
    {
        $this->productRepository->toggleProductStatus(request('product_id'));
        return response()->json(['data' => 'Product Status toggled']);
    }
}

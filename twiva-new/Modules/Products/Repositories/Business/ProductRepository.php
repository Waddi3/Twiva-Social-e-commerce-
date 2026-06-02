<?php


namespace Modules\Products\Repositories\Business;


use Illuminate\Support\Facades\Storage;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;
use Modules\Influencer\Entities\SubCategories;
use Modules\Partner\Enums\ProductShipping;
use Modules\Products\EventTicketProduct;

class ProductRepository {
    public function getProductsToIndex($user_id, $keyword = null, $subcategories = null, $categories = null, $is_published = false, $priceOrder = null)
    {
        $products = Product::select(
            'id',
            'name',
            'brand',
            'price',
            'retail_price',
            'stock',
            'main_image',
            'total_sale',
            'discount',
            'category',
            'sub_category',
            'status',
            'has_shipping_fees',
            'shipping_by',
            'is_ali_express'
        )
            ->with(['productImages' => function ($query)
            {
                $query->select('id', 'image', 'is_cover_pic');
            }])
            ->where('user_id', $user_id)// ->where('stock', '>', 0)
        ;
        if ($keyword)
        {
            $products = $products->where(function ($where) use ($keyword)
            {
                $where->where('name', 'like', "%$keyword%")
                    ->orWhere('brand', 'like', "%$keyword%");
            });
        }
        if ($subcategories)
        {
            $products = $products->whereIn('sub_category', request('subcategories'));
        }
        if ($categories)
        {
            $products = $products->whereIn('sub_category', request('categories'));
        }
        if ($is_published)
        {
            $products = $products->where('status', '1');
        }
        if (in_array($priceOrder, ['ASC', 'DESC']))
        {
            $products = $products->orderBy('price', $priceOrder);
        }
        else
        {
            $products->latest();
        }
        return $products;
    }

    public function getProductToShow($product_id)
    {
        return Product::select(
            'id',
            'name',
            'brand',
            'price',
            'retail_price',
            'stock',
            'main_image',
            'status',
            'description',
            'specification',
            'discount',
            'color',
            'sub_category',
            'category',
            'size',
            'status',
            'created_at'/*, 'has_shipping_fees', 'shipping_by'*/,
            'is_ali_express'
        )
            ->with(
                ['productReviewsAndRatings' => function ($productReviewsAndRatingsQuery)
                {
                    $productReviewsAndRatingsQuery->select('id', 'product_id', 'rating', 'review', 'buyer_id', 'created_at');
                },
                 'eventTicketProduct'])
            ->where('id', $product_id)->firstOrFail();
    }

    public function storeProduct($data)
    {
        $main_image = $data['main_image'];
        unset($data['main_image']);
        $data = $this->prepareProductForDB($data);
        $productImages = $data['images'] ?? [];
        unset($data['images']);
        $product = Product::create($data);
        ProductImage::create(
            [
                'is_cover_pic' => 1,
                'product_id'   => $product->id,
                'image'        => str_replace(ProductImage::PRODUCT_IMAGE_PATH . '/', '', $main_image->store(ProductImage::PRODUCT_IMAGE_PATH, 'public'))
            ]
        );
        foreach ($productImages as $image)
        {
            ProductImage::create(
                [
                    'is_cover_pic' => 0,
                    'product_id'   => $product->id,
                    'image'        => str_replace(ProductImage::PRODUCT_IMAGE_PATH . '/', '', $image->store(ProductImage::PRODUCT_IMAGE_PATH, 'public'))
                ]
            );
        }
        $isEventProduct = SubCategories::where('id', request('sub_category'))
            ->where('name', SubCategories::EventSubCategoryName)->exists();
        if ($isEventProduct)
        {
            $product->update([
                'has_shipping_fees' => false,
                'shipping_by' => ProductShipping::Business->value
            ]);
            EventTicketProduct::create([
                'date'                => $data['date'],
                'start_time'          => $data['start_time'],
                'end_time'            => $data['end_time'],
                'location'            => $data['location'],
                'business_product_id' => $product->id,
                'ticket_number' => EventTicketProduct::generateTicketNumber()
            ]);
        }
        return $product;
    }

    public function updateProduct(Product $product, $data)
    {
        $main_image = $data['main_image'] ?? null;
        $images = $data['images'] ?? null;

        unset($data['main_image']);
        unset($data['images']);

        $data = $this->prepareProductForDB($data);
        $this->updateInfluencerProductPrices(businessProductId: $product->id, oldProductPrice: $product->retail_price, newProductPrice: $data['retail_price']);
        $product->update($data);
        if ($main_image)
        {
            $product->productImages()->update(['is_cover_pic' => 0]);
            ProductImage::create(
                [
                    'is_cover_pic' => 1,
                    'product_id'   => $product->id,
                    'image'        => str_replace(ProductImage::PRODUCT_IMAGE_PATH . '/', '', $main_image->store(ProductImage::PRODUCT_IMAGE_PATH, 'public'))
                ]
            );
        }
        if (isset($images))
        {
            foreach ($images as $image)
            {
                ProductImage::create(
                    [
                        'is_cover_pic' => 0,
                        'product_id'   => $product->id,
                        'image'        => str_replace(ProductImage::PRODUCT_IMAGE_PATH . '/', '', $image->store(ProductImage::PRODUCT_IMAGE_PATH, 'public'))
                    ]
                );
            }
        }
        if ($product->is_event_ticket_product)
        {
            $product->update([
                'has_shipping_fees' => false,
                'shipping_by' => ProductShipping::Business->value
            ]);
            EventTicketProduct::updateOrCreate(['business_product_id' => $product->id], [
                'date'                => $data['date'],
                'start_time'          => $data['start_time'],
                'end_time'            => $data['end_time'],
                'location'            => $data['location'],
                'business_product_id' => $product->id,
            ]);
        }
        return $product->id;
    }

    private function updateInfluencerProductPrices($businessProductId, $oldProductPrice, $newProductPrice)
    {
        $influencerShopProducts = InfluencerShopProducts::where('product_id', $businessProductId)->get();
        foreach ($influencerShopProducts as $influencerShopProduct)
        {
            $diff = $influencerShopProduct->product_price - $oldProductPrice;
            $influencerShopProduct->update(['product_price' => $diff + $newProductPrice]);
        }
    }

    public
    function deleteProduct($product_id)
    {
        $product = Product::where('user_id', auth()->id())->where('id', $product_id)->firstOrFail();
        //        $product->forInfluencers()->sync([]);
        $productImages = $product->productImages()->delete();
        $product->delete();
    }

    public
    function deleteImageFromProduct($product_id, $image_id)
    {
        $product = Product::where('user_id', auth()->id())->where('id', $product_id)->firstOrFail();
        $image = ProductImage::where('id', $image_id)->firstOrFail();
        $image_path = ProductImage::PRODUCT_IMAGE_PATH . '/' . $image->image;
        Storage::disk('public')->delete($image_path);
        $image->delete();
        if ($image->is_cover_pic)
        {
            $next_product_image = $product->productImages()->first();
            if ($next_product_image)
            {
                $next_product_image->update(['is_cover_pic' => 1]);
            }
        }
        return response()->json(['data' => $image_path . ' Product Image Deleted Successfuly']);
    }

    private
    function prepareProductForDB($data)
    {
        $data['user_id'] = auth('api')->id();
        $data['status'] = 1;
        $data['invite'] = 0;
        if (isset($data['colors']))
        {
            $data['color'] = implode(',', $data['colors']);
            unset($data['colors']);
        }
        else
        {
            $data['color'] = null;
        }

        if (isset($data['specifications']))
        {
            $data['specification'] = implode(',', $data['specifications']);
            unset($data['specifications']);
        }
        else
        {
            $data['specification'] = null;
        }
        if (isset($data['sizes']))
        {
            $data['size'] = implode(',', $data['sizes']);
            unset($data['sizes']);
        }
        else
        {
            $data['size'] = null;
        }
        $data['shipping_by'] = ProductShipping::Business->value;
        if (isset($data['shipping_by_partner']))
        {
            if ($data['shipping_by_partner'])
            {
                $data['shipping_by'] = ProductShipping::Partner->value;
            }
            unset($data['shipping_by_partner']);
        }

        return $data;
    }

    public function toggleProductStatus($productID)
    {
        $product = Product::findOrFail($productID);
        $product->update(['status' => !$product->status]);
        return $product->status;
    }
}

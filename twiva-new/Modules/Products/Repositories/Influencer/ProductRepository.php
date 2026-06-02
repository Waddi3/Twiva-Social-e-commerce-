<?php


namespace Modules\Products\Repositories\Influencer;


use Illuminate\Support\Facades\Storage;
use Modules\Influencer\Entities\InfluencerShopProductImages;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Notifications\Business\ProductAddedToInfluencerShop;


class ProductRepository
{
    public function getShopProductsToIndex($shop_id, $keyword = null, $categories = null, $priceOrder = null)
    {
        $influencerShopProducts = InfluencerShopProducts::where('shop_id', $shop_id)->whereHas('product')
            ->where('status', 1)
            ->with(
                ['productMainImage' => function ($q) {
//                    $q->select('image_path', 'shop_product_id', 'id');
                }]
                , 'businessProductMainImages',
                [
                    'product' => function ($product) {
                        $product->select(
                            'id', 'category', 'sub_category', 'color', 'size', 'specification', 'price',
                            'retail_price', 'discount', 'stock', 'brand', 'shipping_by', 'is_ali_express'
                        )
                            ->where('status', 1)
                            ->where('stock', '>', 0);
                    }
                ], [
                'productReviewsAndRatings' => function ($productReviewsAndRatings) {
                    $productReviewsAndRatings->select('id', 'rating', 'product_id');
                }
            ])
            ->select('id', 'shop_id', 'product_id', 'product_title', 'product_price');

        if ($keyword) {
            $influencerShopProducts = $influencerShopProducts->where(function ($where) use ($keyword) {
                $where->where('product_title', 'LIKE', "%$keyword%")->orWhereHas('product', function ($or_where_has_product) use ($keyword) {
                    $or_where_has_product->where('brand', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($categories) {
            $influencerShopProducts = $influencerShopProducts->whereHas('product', function ($q) use ($categories) {
                $q->whereIn('sub_category', $categories);
            });
        }
        if ($priceOrder) {
            $influencerShopProducts = $influencerShopProducts->orderBy('product_price', $priceOrder);
        } else {
            $influencerShopProducts = $influencerShopProducts->latest();
        }

        return $influencerShopProducts->paginate(20);
    }

    public function showShopProduct($shop_id)
    {
        return InfluencerShopProducts::where('shop_id', $shop_id)
            ->where('status', 1)
            ->where('id', request('product_id'))
            ->select('id', 'product_id', 'shop_id', 'product_title', 'product_cover_image', 'product_description', 'product_price')
            ->with(['productMainImage', 'influencerProductImages', 'BusinessProductImages', 'businessProductMainImage', 'product'])
            ->firstOrFail();
    }

    public function storeShopProduct($shop_id, $data, $influencer_id = null)
    {
        if (array_key_exists('products', $data)) {
            foreach ($data['products'] as $product) {
                $product_business = Product::find($product['product_id']);
                if ($product_business) {
                    $product['shop_id'] = $shop_id;
                    $product['influencer_id'] = auth('api')->id() ?? $influencer_id;
                    $product['status'] = 1;
                    $product['product_price'] = $product_business->product_price;
                    $influencerProduct = InfluencerShopProducts::create($product);
                    if (isset($product['product_images'])) {
                        foreach ($product['product_images'] as $product_image) {
                            $influencerProductImage['image_path'] = str_replace(
                                InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH . '/',
                                '',
                                $product_image->store(InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH, 'public')
                            );
                            $influencerProductImage['shop_product_id'] = $influencerProduct->id;
                            $influencerProductImage['is_cover_pic'] = 0;
                            InfluencerShopProductImages::create($influencerProductImage);
                        }
                    }
                    if (isset($product['main_product_image'])) {
                        $influencerProductImage['image_path'] = str_replace(
                            InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH . '/',
                            '',
                            $product['main_product_image']->store(InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH, 'public')
                        );
                        $influencerProductImage['shop_product_id'] = $influencerProduct->id;
                        $influencerProductImage['is_cover_pic'] = 1;
                        InfluencerShopProductImages::create($influencerProductImage);
                    }
                    $business = $product_business->business;
                    if ($business) {
                        $business->notify(new ProductAddedToInfluencerShop($product_business));
                    }
                }
            }
        }
    }

    public function updateShopProduct($data)
    {
        $product = InfluencerShopProducts::find($data['id']);
        if (isset($data['product_images'])) {
            foreach ($data['product_images'] as $product_image) {
                $influencerProductImage['image_path'] = str_replace(
                    InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH . '/',
                    '',
                    $product_image->store(InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH, 'public')
                );
                $influencerProductImage['shop_product_id'] = $product->id;
                $influencerProductImage['is_cover_pic'] = 0;
                InfluencerShopProductImages::create($influencerProductImage);
            }
        }
        if (isset($data['main_product_image'])) {
            $product->influencerProductImages()->update(['is_cover_pic' => 0]);
            $influencerProductImage['image_path'] = str_replace(
                InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH . '/',
                '',
                $data['main_product_image']->store(InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH, 'public')
            );
            $influencerProductImage['shop_product_id'] = $product->id;
            $influencerProductImage['is_cover_pic'] = 1;
            InfluencerShopProductImages::create($influencerProductImage);
        }
        $product->update($data);
    }

    public function deleteShopProductImage($shop_id, $product_id, $image_id)
    {
        $product = InfluencerShopProducts::where('shop_id', $shop_id)->where('id', $product_id)->firstOrFail();
        $image = $product->influencerProductImages()->where('id', $image_id)->firstOrFail();
        $image_path = InfluencerShopProductImages::INFLUENCER_PRODUCT_IMAGE_PATH . '/' . $image->image_path;
        Storage::disk('public')->delete($image_path);
        $image->delete();
        if ($image->is_cover_pic) {
            $next_product_image = $product->influencerProductImages()->first();
            if ($next_product_image) {
                $next_product_image->update(['is_cover_pic' => 1]);
            }
        }
    }

    public function deleteShopProduct($shop_id, $product_id)
    {
        $product = InfluencerShopProducts::where('shop_id', $shop_id)->where('id', $product_id)->firstOrFail();
        $product->influencerProductImages()->delete();
        $product->delete();
    }
}

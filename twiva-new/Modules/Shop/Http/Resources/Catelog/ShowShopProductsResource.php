<?php

namespace Modules\Shop\Http\Resources\Catelog;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowShopProductsResource extends JsonResource
{
    private $shopProduct;

    public function __construct($shopProduct)
    {
        $this->shopProduct = $shopProduct;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->shopProduct->id,
            'name' => $this->shopProduct->product_title,
            'price' => $this->shopProduct->product_price,
            'business_price' => $this->shopProduct->product->price,
            'retail_price' => $this->shopProduct->product->retail_price,
            'business_discount' => $this->shopProduct->product->discount ?? $this->shopProduct->product->retail_price, // check it
            'business_discount_rate' => $this->shopProduct->product->discountRate ?? 0,
            'business_earning' => $this->shopProduct->product->businessEarning(),
            'stock' => (int)$this->shopProduct->product->stock ?? 0,
            'description' => $this->shopProduct->product_description,
            'influencer_main_image_path' => $this->shopProduct->productMainImage ? $this->shopProduct->productMainImage->imageUrl : null,
            'main_image_path' => $this->shopProduct->businessProductMainImage ? $this->shopProduct->businessProductMainImage->imageUrl : null,
            'influencer_product_cover_image' => $this->shopProduct->product_cover_image,
            'influencerProductImages' => $this->shopProduct->influencerProductImages->map(function ($image) {
                    $image->imageUrl = $image->imageUrl;
                    return collect($image->toArray())->only('id', 'imageUrl') ?? [];
                }) ?? [],
            'BusinessProductImages' => $this->shopProduct->BusinessProductImages->map(function ($image) {
                $image->imageUrl = $image->imageUrl;
                return collect($image->toArray())->only('id', 'imageUrl');
            }),
            'shipping_by_partner' => $this->shopProduct->product->shipping_by == 'partner' ? 1 : 0,
            'colors' => $this->shopProduct->product->colorDetails,
            'sizes' => $this->shopProduct->product->sizeDetails,
            'specifications' => $this->shopProduct->specifications,
            'influencer_price' => $this->shopProduct->product_price,
            'influencerPrice' => $this->shopProduct->product_price,
            'brand' => $this->shopProduct->product->brand,
            'is_ali_express' => $this->shopProduct->product->is_ali_express,
        ];
    }
}

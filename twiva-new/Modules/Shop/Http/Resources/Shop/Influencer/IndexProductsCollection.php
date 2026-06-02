<?php

namespace Modules\Shop\Http\Resources\Shop\Influencer;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexProductsCollection extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->product_title,
            'price' => $this->product_price,
            'business_price' => $this->product->price,
            'retail_price' => $this->product->retail_price,
            'business_discount' => $this->product->discount ?? $this->product->retail_price,
            'business_discount_rate' => $this->product->discountRate,
            'business_earning' => $this->product->businessEarning(),
            'main_image_path' => $this->businessProductMainImage->imageUrl ?? null,
            'influencer_main_image_path' => $this->productMainImage->imageUrl ?? null,
            'category' => $this->categoryDetails,
            'stock' => (int)$this->product?->stock ?? 0,
            'sub_category' => $this->subcategoryDetails,
            'specifications' => $this->specifications,
            'ratings' => $this->ratings ?? 5,
            'colors' => $this->product?->colorDetails ?? [],
            'sizes' => $this->product?->sizeDetails ?? [],
            'influencer_price' => $this->product_price,
            'influencerPrice' => $this->product_price,
            'brand' => $this->product->brand,
            'product_is_ali_express' => $this->product->is_ali_express,
        ];
    }
}

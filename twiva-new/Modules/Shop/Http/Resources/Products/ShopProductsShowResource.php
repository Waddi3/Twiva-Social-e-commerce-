<?php

namespace Modules\Shop\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductsShowResource extends JsonResource
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
            'stock' => $this->shopProduct->actualDetail->stock ?? 0,
            'description' => $this->shopProduct->product_description,
            'influencer_main_image_path' => $this->shopProduct->productMainImage ? $this->shopProduct->productMainImage->only('id', 'imageUrl') : null,
            'influencer_product_cover_image' => $this->shopProduct->product_cover_image,
            'influencerProductImages' => $this->shopProduct->influencerProductImages->map(function ($image) {
                    $image->imageUrl = $image->imageUrl;
                    return collect($image->toArray())->only('id', 'imageUrl') ?? [];
                }) ?? [],
            'BusinessProductImages' => $this->shopProduct->BusinessProductImages->map(function ($image) {
                $image->imageUrl = $image->imageUrl;
                return collect($image->toArray())->only('id', 'imageUrl');
            }),
        ];
    }
}

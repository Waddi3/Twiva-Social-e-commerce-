<?php

namespace Modules\Shop\Http\Resources\Shop\Influencer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowProductResource extends ResourceCollection
{
    private $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'description' => $this->product->description,
            'price' => (float)$this->product->price,
            'retail_price' => $this->product->retail_price,
            'business_discount' => $this->product->discount ?? $this->product->retail_price,
            'business_discount_rate' => $this->product->discountRate,
            'business_earning' => $this->product->businessEarning(),

            'stock' => (float)$this->product->stock,
            'size' => $this->product->size,
            'main_image_path' => $this->product->mainImagePath,
            'BusinessProductImages' => $this->product->productImages->map(function ($image) {
                $image->imageUrl = $image->imageUrl;
                return collect($image->toArray())->only('id', 'imageUrl');
            }),
            'specification' => $this->product->specifications,
            'color' => $this->product->colorDetails,
            'colors' => $this->product->colorDetails,
            'sizes' => $this->product->sizeDetails,

            'categotry' => $this->product->categoryDetails,
            'sub_category' => $this->product->subcategoryDetails,
            'brand' => $this->product->brand,
        ];
    }
}

<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'user_id' => $this->user_id,
            'name' => $this->name,
            // 'title' => $this->title,
            'brand' => $this->brand,
            'status' => $this->status,
            'stock' => Product::getStock($this),
            'price' => Product::getPrice($this),
            'discount' => Product::getDiscountPrice($this),
            'main_image' => $this->main_image_path,
            'category' => $this->category?->only('id', 'name'),
            'sub_category' => $this->sub_category?->only('id', 'name'),
            // 'discount_rate' => $this->discount_rate,
            // 'description' => $this->description,
            // 'specification' => $this->specification,
            // 'color' => $this->color,
            // 'size' => $this->size,
            // 'has_shipping_fees' => $this->has_shipping_fees,
            'colors' => $this->color_details,
            'sizes' => $this->size_details,
            // 'images' => Product::getImages($this),
        ];
    }
}

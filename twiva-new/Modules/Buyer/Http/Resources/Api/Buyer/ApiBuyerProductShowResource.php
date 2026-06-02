<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerProductShowResource extends JsonResource
{

    public $similar_products;
    public function __construct($resource, $similar_products)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->similar_products = $similar_products;
    }

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
            'has_shipping_fees' => $this->has_shipping_fees,
            // 'discount_rate' => $this->discount_rate,
            'description' => $this->description,
            'specification' => $this->specification,
            'category' => $this->category?->only('id', 'name'),
            'sub_category' => $this->sub_category?->only('id', 'name'),
            // 'color' => $this->color,
            // 'size' => $this->size,
            'colors' => $this->color_details,
            'sizes' => $this->size_details,
            // 'main_image' => $this->main_image_path,
            'images' => Product::getImages($this),
            'similar_products' => ApiBuyerProductResource::collection($this->similar_products),
            'product_is_ali_express' => Product::getIsAliExpress($this),
        ];
    }
}

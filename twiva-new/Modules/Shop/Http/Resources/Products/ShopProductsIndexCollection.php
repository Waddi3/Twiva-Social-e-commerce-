<?php

namespace Modules\Shop\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductsIndexCollection extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
//            'name' => $this->product_title,
//            'price' => $this->product_price,
//            'main_image_path' => $this->productMainImage->imageUrl ?? null,
//            'category' => $this->categoryDetails,
//            'sub_category' => $this->subcategoryDetails,
            'colors' => $this->actualDetail,
//            'sizes' => $this->actualDetail->sizeDetails,
//            'specifications' => $this->specifications,
        ];
    }
}

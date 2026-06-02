<?php

namespace Modules\Shop\Http\Resources\Catelog;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexProductsToAddCollection extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'retail_price' => $this->retail_price,
            'business_discount' => $this->discount ?? $this->retail_price,
            'business_discount_rate' => $this->discountRate,
            'business_earning' => $this->businessEarning(),
            'main_image_path' => $this->mainImagePath,
            'category' => $this->categoryDetails,
            'sub_category' => $this->subcategoryDetails,
            'colors' => $this->ColorDetails,
            'sizes' => $this->SizeDetails,
            'specifications' => $this->specifications,
            'description' => $this->description,
            'brand' => $this->brand,
        ];
    }
}

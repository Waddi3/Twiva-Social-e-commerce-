<?php

namespace Modules\Shop\Http\Resources\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsToAddIndexCollection extends JsonResource
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
            'main_image_path' => $this->mainImagePath,
            'category' => $this->categoryDetails,
            'sub_category' => $this->subcategoryDetails,
            'colors' => $this->ColorDetails,
            'sizes' => $this->SizeDetails,
            'specifications' => $this->specifications,
        ];
    }
}

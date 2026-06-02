<?php

namespace Modules\Shop\Http\Resources\Catelog;

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
            'stock' => (float)$this->product->stock,
            'size' => $this->product->size,
            'main_image_path' => $this->product->mainImagePath,
            'specification' => $this->product->specifications,
            'color' => $this->product->colorDetails,
            'categotry' => $this->product->categoryDetails,
            'sub_category' => $this->product->subcategoryDetails,


        ];
    }
}

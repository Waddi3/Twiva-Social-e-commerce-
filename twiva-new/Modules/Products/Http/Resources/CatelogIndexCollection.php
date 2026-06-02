<?php

namespace Modules\Products\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Partner\Enums\ProductShipping;

class CatelogIndexCollection extends JsonResource
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
            'brand' => $this->brand,
            'price' => $this->price,
            'retail_price' => $this->retail_price,
            'stock' => (int)$this->stock,
            'total_sale' => (int)$this->total_sale,
            'main_image_path' => $this->mainImagePath,
            'discount' => $this->discount,
            'discount_percent' => $this->discountRate,
            'has_shipping_fees' => $this->has_shipping_fees,
            'shipping_by_partner' => $this->shipping_by == ProductShipping::Partner->value ? 1 : 0,
            'is_ali_express' => $this->is_ali_express,
        ];
    }
}

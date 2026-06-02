<?php

namespace Modules\Buyer\Http\Resources\Api\Buyer\Order;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Influencer\Entities\Product;

class ApiBuyerOrdersDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'products' => $this->getProductsDetails(),
        ];
    }
}

<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Respositories\Business\OrderRepository;

class IndexOrdersCollection extends JsonResource
{
    public function __construct($orders)
    {
        parent::__construct($orders);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
//            'amount' => OrderRepository::calculateBusinessAmountFromOrderWithShipping($this),
            'amount' => $this->BusinessAmount,
            'status' => $this->statusString,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}

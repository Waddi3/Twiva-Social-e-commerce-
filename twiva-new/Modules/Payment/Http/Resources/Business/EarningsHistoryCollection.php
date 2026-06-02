<?php

namespace Modules\Payment\Http\Resources\Business;

use Illuminate\Http\Resources\Json\JsonResource;

class EarningsHistoryCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->id,
            'quantity' => $this->Qty,
            'amount' => $this->businessAmount,
            'status' => $this->order_status,
            'created_at' => $this->created_at,
            'order' => $this->modifyPrice($this),
        ];
    }

    public function modifyPrice($order)
    {
        return (object)['id' => $order->id, 'order_amount' => $order->businessAmount, 'buyer_info' => $order->buyer_info];
    }
}

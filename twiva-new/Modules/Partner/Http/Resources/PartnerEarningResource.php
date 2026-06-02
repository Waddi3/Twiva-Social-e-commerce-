<?php

namespace Modules\Partner\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerEarningResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->id,
            'quantity' => $this->Qty,
            'shipping_amount' => $this->shipping_amount,
            'order_amount' => $this->order_amount,
            'status' => $this->order_status,
            'created_at' => $this->created_at,
            //'order' => $this->modifyPrice($this),
        ];
    }

    /*public function modifyPrice($order)
    {
        return (object)['id' => $order->id, 'order_amount' => $order->businessAmount, 'buyer_info' => $order->buyer_info];
    }*/
}

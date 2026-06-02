<?php

namespace Modules\Payment\Http\Resources\Business;

use Illuminate\Http\Resources\Json\JsonResource;

class EarningsHistoryCollection2 extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order' => $this->Order->only('id','order_amount','buyer_info'),
//            'buyer_info' => $this->Order->buyerData?->only('id', 'email') ?? (object)['id' => 0, 'email' => 'guest@gmail.com'],
            'amount' => $this->businessAmount,
            'order_date' => $this->Order->created_at,
            'qty' => $this->Order->qty ?? '-',
            'delivery_date' => $this->Order->delivery_date ?? '-',

        ];
    }
}

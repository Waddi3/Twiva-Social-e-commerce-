<?php

namespace Modules\Payment\Http\Resources\Business;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryIndexCollection extends JsonResource
{
    public function __construct($paymentHistory)
    {
        parent::__construct($paymentHistory);

    }

    public function toArray($request)
    {
        return [
            'amount' => $this->transaction_amount,
            'created_at' => Carbon::parse($this->created_at)->format('d M, Y'),
        ];
    }
}

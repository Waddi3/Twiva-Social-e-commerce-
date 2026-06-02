<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class ApiBuyerPaymentRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'order_ids' => ['required', 'array'],
            'order_ids.*' => ['required', 'exists:orders,id'],
            'transaction_id' => ['required', 'string']
        ];
    }
}

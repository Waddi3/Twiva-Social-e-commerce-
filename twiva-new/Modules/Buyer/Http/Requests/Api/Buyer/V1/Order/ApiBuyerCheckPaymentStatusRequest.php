<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiBuyerCheckPaymentStatusRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'transaction_id' => [
                Rule::requiredIf(function () {
                    return $this->payment_type == 'MPESA';
                }),
                'string'
            ],
            'payment_type' => ['required', 'string', 'in:MPESA,PayOnDelivery']
        ];
    }
}

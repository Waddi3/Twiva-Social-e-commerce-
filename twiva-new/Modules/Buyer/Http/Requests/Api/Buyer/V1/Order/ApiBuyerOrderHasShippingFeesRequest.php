<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiBuyerOrderHasShippingFeesRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'products' => ['required', 'array']
        ];
    }
}

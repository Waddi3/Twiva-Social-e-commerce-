<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBusinessShippingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'has_shipping_fees' => ['required ', 'integer'],
            'shipping_by_partner' => ['required ', 'integer'],
            'pay_on_delivery' => ['required ', 'integer'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

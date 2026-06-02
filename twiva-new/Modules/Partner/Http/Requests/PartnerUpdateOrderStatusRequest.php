<?php

namespace Modules\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerUpdateOrderStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ];
    }
}

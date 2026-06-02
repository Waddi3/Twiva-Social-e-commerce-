<?php

namespace Modules\Buyer\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Buyer\Entities\Invoice;

class ApiCustomerInvoiceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'buyer_id' => ['required', 'exists:buyers,id'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required'], // check if product Business or Influencer
            'products.*.quantity' => ['required', 'numeric', 'min:1', 'max:1000'],
            'due_date' => ['required', 'date', 'date_format:' . Invoice::FORMAT_DATA, 'after_or_equal:today'],
            'message' => ['required', 'string'],
        ];
    }
}

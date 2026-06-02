<?php

namespace Modules\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerOrdersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'numeric'],
            'type' => ['required'],
            'keyword' => ['regex:/[a-zA-Z0-9\s]+/']
        ];
    }
}

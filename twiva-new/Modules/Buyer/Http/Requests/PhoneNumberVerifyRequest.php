<?php

namespace Modules\Buyer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneNumberVerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'numeric']
        ];
    }
}

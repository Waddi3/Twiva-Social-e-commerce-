<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendDeliveryCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mobile' => ['required'],
        ];
    }
}

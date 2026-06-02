<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerfiyOTPRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required'],
            'email' => ['required', 'exists:users,email', 'email'],
        ];
    }
}
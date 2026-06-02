<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOTPRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['email', 'required', 'exists:users,email'],
            'otp' => ['required'],
            'device_token' => ['nullable', 'string']
        ];
    }
}
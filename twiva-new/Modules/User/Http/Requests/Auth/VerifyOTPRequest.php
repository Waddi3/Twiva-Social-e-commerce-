<?php

namespace Modules\User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOTPRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'device_token' => ['required'],
            'device_type' => ['required']
        ];
    }
}
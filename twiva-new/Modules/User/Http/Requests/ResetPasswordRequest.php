<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required'],
            'email' => ['required', 'exists:users,email', 'email'],
            'password' => ['string', 'min:8', 'confirmed']
        ];
    }
}
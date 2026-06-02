<?php

namespace Modules\Buyer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordAfterForgetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['exists:password_resets,token', 'required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
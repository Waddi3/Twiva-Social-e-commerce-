<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendOTPToVerifyPhoneOrEmail extends FormRequest
{
    public function rules(): array
    {
        return [
            'for' => ['required', 'in:EMAIL,MOBILE']
        ];
    }
}
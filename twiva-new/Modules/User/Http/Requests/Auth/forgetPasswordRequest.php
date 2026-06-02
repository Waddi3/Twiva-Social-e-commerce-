<?php

namespace Modules\User\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class forgetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required','email', Rule::exists('users', 'email')->whereNull('deleted_at')]
        ];
    }
}

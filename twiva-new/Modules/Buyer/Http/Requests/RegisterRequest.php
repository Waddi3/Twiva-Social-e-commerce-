<?php

namespace Modules\Buyer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Buyer\Rules\UniqueIf;

class RegisterRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        if ($this->mobile) {
            $data = [
                'phone_number' => explode('-', $this->mobile)[1]
            ];
            $data['phone_number'] = str_replace(' ', '', $data['phone_number']);
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email', 'unique:buyers,email'],
            'mobile' => ['required', 'string', 'phone:AUTO,KE'],
            'phone_number' => ['required', Rule::unique('buyers', 'phone_number')->where('is_external', 0)  /*, new UniqueIf('is_verified', true)*/, /*, Rule::unique('buyers', 'phone_number')*/],
            'password' => ['required', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

}

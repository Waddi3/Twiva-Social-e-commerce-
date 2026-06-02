<?php

namespace Modules\Buyer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgetPasswordRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $username = filter_var($this->email, FILTER_VALIDATE_EMAIL);
        if (!$username) {
            $data = [
                'email' => null,
                'mobile' => LoginRequest::filterPhoneNumber($this->email),
            ];
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
//            'email' => ['required', 'email', 'exists:buyers,email'],
            'email' => [
//                'required',
                'nullable',
                Rule::requiredIf(function () {
                    return $this->email;
                }),
                'email',
                'exists:buyers,email',
            ],
            'mobile' => [
                'nullable',
                'exists:buyers,phone_number',
//                'phone:AUTO,KE',
                Rule::requiredIf(function () {
                    return is_null($this->email);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'mobile.exists' => 'The selected phone number is invalid.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

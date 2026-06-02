<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterBusinessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'contact_name' => ['required', 'string'],
            'country' => ['required', 'exists:countries,id'],
            'city' => ['required', 'exists:cities,id'],
            'state' => ['required', 'exists:states,id'],
            'area' => ['required', 'string'],
            'description' => ['required', 'string'],
            'phone_number' => ['required', 'string', Rule::unique('users')->where(function ($query) {
                return $query->where('phone_number', $this->phone_number)
                    ->where('phone_number', '!=', '728226977')
                    ->where('phone_number', '!=', '706294735')
                    ->where('phone_number', '!=', '799402657')
                    ->where('phone_number', '!=', '9056176337')
                    ->where('phone_number', '!=', '9184231428')

                    ->where('phone_code', $this->phone_code);
            }),
            ],
            'phone_code' => ['required', 'string'],
            'invitor_referral_code' => ['nullable', 'string'],
            'logo' => ['nullable', 'image'],
        ];

    }
}

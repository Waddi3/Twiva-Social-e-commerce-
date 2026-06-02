<?php

namespace Modules\Influencer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterInfluencerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string'],
            'location' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'gender' => ['required', "in:MALE,FEMALE,NOT_SPECIFIED"],
            'description' => ['nullable', 'string'],
            'phone_number' => ['required', 'string',
                Rule::unique('users')->where(function ($query) {
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
            'profile_image' => ['nullable', 'image'],

            'interests' => ['nullable', 'array'],
            'interests.*' => ['required', 'exists:categories,id'],

            'content_type' => ['nullable', 'array'],
            'content_type.*' => ['required', 'exists:content_types,id'],

        ];
    }
}

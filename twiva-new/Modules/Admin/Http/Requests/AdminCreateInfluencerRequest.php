<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateInfluencerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string'],
            'location' => ['nullable', 'string'],
            'dob' => ['required', 'date'],
            'gender' => ['required', "in:MALE,FEMALE,NOT_SPECIFIED"],
            'description' => ['nullable', 'string'],

            'phone_number' => ['nullable', 'string'],
            'phone_code' => ['nullable', 'string'],

            'invitor_referral_code' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image'],

            'interests' => ['required', 'array'],
            'interests.*' => ['required', 'exists:categories,id'],

            'content_type' => ['nullable', 'array'],
            'content_type.*' => ['required', 'exists:content_types,id'],

        ];
    }
}

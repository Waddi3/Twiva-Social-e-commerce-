<?php

namespace Modules\Influencer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfluencerRequest extends FormRequest
{

    public function rules(): array
    {
        $this->messages = [
            'dob.required' => 'The Date Of Birth is required',
            'dob.before' => 'You must be at least 15 years old',
        ];
        return [
            'name' => 'required',
            'gender' => ['required', "in:MALE,FEMALE,NOT_SPECIFIED"],
            'description' => 'required',
            'profile_image' => ['nullable', 'image'],
            'location' => ['required', 'string'],
            'content_type' => ['required', 'exists:content_types,id'],
            'dob' => 'required | before:15 years ago',
            'interests' => ['required', 'array'],
            'interests.*' => ['required', 'exists:categories,id'],
            'phone_number' => ['required', 'string'],
            'phone_code' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

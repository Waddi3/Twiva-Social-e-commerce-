<?php

namespace Modules\Influencer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InfluncerDomainRequest extends FormRequest
{
    public function messages()
    {

        return [
            'regex' => 'Domain must starts with character and only contain small characters and numbers'
        ];
    }

    public function rules(): array
    {
        return [
            'domain' => [
                Rule::unique('shops', 'domain')->ignore(auth('api')->id(),'user_id'),
                'regex:/^[0-9a-z][a-z0-9]*/',
                'alpha_dash',
                'min:4',
                'string'
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
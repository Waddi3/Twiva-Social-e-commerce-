<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdatePartnerRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'contact_name' => ['required', 'string'],
            'country' => ['required', 'exists:countries,id'],
            'city' => ['required', 'exists:cities,id'],
            'state' => ['required', 'exists:states,id'],
            'area' => ['required', 'string'],
            'description' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'phone_code' => ['required', 'string'],
            'invitor_referral_code' => ['nullable', 'string'],
        ];
    }


}

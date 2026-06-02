<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBusinessDetailsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required ', 'string'],
            'contact_name' => 'required',
            'logo' => ['nullable', 'image'],
            'phone_number' => ['required'],
            'phone_code' => ['required'],
            'description' => ['required','string'],
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'area' => 'required',
            'pay_on_delivery' => 'boolean'

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

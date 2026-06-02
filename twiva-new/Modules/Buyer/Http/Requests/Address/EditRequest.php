<?php

namespace Modules\Buyer\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'delivery_address' => ['required', 'string'],
            'country_id' => ['nullable', 'string', 'exists:countries,id'],
            'county_id' => ['nullable', 'string', 'exists:states,id'],
            'city_id' => ['nullable', 'string', 'exists:cities,id'],
            'email' => ['nullable', 'email'],
            'phone_number' => ['required', 'string', 'phone:AUTO,KE'],
            'latitude' => ['nullable', 'min:-90', 'max:90'], // required
            'longitude' => ['nullable', 'min:-180', 'max:180'] // required
        ];
    }
}

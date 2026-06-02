<?php

namespace Modules\Buyer\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'delivery_address' => ['required', 'string'],
            'country_id' => ['required', 'string', 'exists:countries,id'],
            'county_id' => ['required', 'string', 'exists:states,id'],
            'city_id' => ['required', 'string', 'exists:cities,id'],
            'email' => ['nullable', 'email'],
            'phone_number' => ['required', 'string', 'phone:AUTO,KE'],
            'latitude' => ['nullable', 'min:-90', 'max:90'], // required
            'longitude' => ['nullable', 'min:-180', 'max:180'] // required
        ];
    }
}

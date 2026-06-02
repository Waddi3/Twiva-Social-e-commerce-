<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiBuyerProfileAddressCreateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:1', 'max:100'],
            'last_name' => ['required', 'string', 'min:1', 'max:100'],
            'delivery_address' => ['required', 'string', 'min:1', 'max:255'],
            'country_id' => ['required', 'string', 'exists:countries,id'],
            'county_id' => ['required', 'string', 'exists:states,id'],
            'city_id' => ['required', 'string', 'exists:cities,id'],
            'phone_number' => ['required', 'string', 'phone:AUTO,KE'],
            'email' => ['nullable', 'email', Rule::unique('buyers', 'email')],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ];
    }
}

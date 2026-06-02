<?php

namespace Modules\Buyer\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Buyer\Entities\Buyer;

class ApiCustomerUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'phone_code' => Str::replace('+', '', request('phone_code')),
            'phone_number' => Str::startsWith(request('phone_number'), '0') ? Str::replaceFirst('0', '', Buyer::removeSpace(request('phone_number'))) : Buyer::removeSpace(request('phone_number')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:50'],
//            'first_name' => ['required', 'string', 'min:1', 'max:50'],
//            'last_name' => ['required', 'string', 'min:1', 'max:50'],
            'email' => ['nullable', 'email', Rule::unique('buyers', 'email')->ignore($this->id, 'id')],
            // TODO::H add verified column to rule check phone code and phone number
            'phone_code' => ['required', 'string', 'min:2', 'max:8'],
            'phone_number' => [
                'required',
                'string',
//                'phone:AUTO,KE',
                'min:1',
                'max:50',
                Rule::unique('buyers', 'phone_number')->ignore($this->id, 'id'),
//                    ->where('is_verified', 1)
            ], // if not verified, must update infos
            'password' => ['nullable', 'confirmed'],
            'password_confirmation' => ['nullable'],
            // address
            'delivery_address' => ['nullable', 'string', 'min:1', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'county_id' => ['nullable',
                Rule::exists('states', 'id')
                    ->where('country_id', $this->country_id)],
            'city_id' => ['nullable',
                Rule::exists('cities', 'id')
                    ->where('state_id', $this->county_id)
                    ->where('country_id', $this->country_id)
            ],
            'latitude' => ['nullable', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'min:-180', 'max:180']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

}

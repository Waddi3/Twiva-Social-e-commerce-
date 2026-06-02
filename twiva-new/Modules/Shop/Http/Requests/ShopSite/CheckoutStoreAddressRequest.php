<?php

namespace Modules\Shop\Http\Requests\ShopSite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutStoreAddressRequest extends FormRequest
{
    public function messages()
    {
        return [
            'address_id' => 'The Address field is required.'
        ];
    }

    protected function prepareForValidation()
    {
        if (Auth::guard('buyer')->guest()) {
            $this->merge(['address_type' => 'new']);
        }
    }

    public function rules(): array
    {
        $buyerAddress = implode(',', Auth::guard('buyer')->user()?->addresses()?->pluck('id')?->toArray() ?? []) ?? [];
        if (request('address_type') == 'exists') {
            return [
                'address_type' => ['required', 'in:exists,new'],
                'address_id' => [Rule::requiredIf(request('address_type') == 'exists'), "in:$buyerAddress", 'nullable'],
                'delivery_instructions' => ['nullable', 'string']
            ];
        } else {
            return [
                'address_type' => ['required', 'in:exists,new'],
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'delivery_address' => ['required', 'string'],
                'country_id' => ['required', 'string', 'exists:countries,id'],
                'county_id' => ['required', 'string', 'exists:states,id'],
                'city_id' => ['required', 'string', 'exists:cities,id'],
                'email' => ['nullable', 'email'],
                'phone_number' => ['required', 'string', 'phone:AUTO,KE'],
                'latitude' => ['nullable', 'min:-90', 'max:90'], // required
                'longitude' => ['nullable', 'min:-180', 'max:180'], // required
                'delivery_instructions' => ['nullable', 'string']
            ];
        }
    }


    public function authorize(): bool
    {
        return true;
    }
}

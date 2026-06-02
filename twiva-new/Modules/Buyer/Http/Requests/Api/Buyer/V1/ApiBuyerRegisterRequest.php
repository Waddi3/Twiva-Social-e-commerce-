<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Buyer\Entities\Buyer;
use Modules\User\Entities\User;

class ApiBuyerRegisterRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'phone_code' => Str::replace('+', '', request('phone_code')),
            'phone_number' => Str::startsWith(request('phone_number'), '0') ? Str::replaceFirst('0', '', Buyer::removeSpace(request('phone_number'))) : Buyer::removeSpace(request('phone_number')),
            'mpesa_mini_app' => filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        $user = User::fromShop(request('account'));
        if(!$user){
            return [
                'shop_domain' => ['required'],
            ];
        }
        return [
            'name' => [
                Rule::requiredIf(function () {
                    return !filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
                }),
                'string',
                'min:1',
                'max:50'
            ],
            //            'first_name' => ['nullable', 'string', 'min:1', 'max:50'],
            //            'last_name' => ['nullable', 'string', 'min:1', 'max:50'],
            'email' => ['nullable', 'email'], //  Rule::unique('buyers', 'email')
            'phone_code' => [
                Rule::requiredIf(function () {
                    return !filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
                }),
                'string',
                'min:2',
                'max:8'
            ],
            'phone_number' => [
                Rule::requiredIf(function () {
                    return !filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
                }),
                'string',
                //                'phone:AUTO,KE',
                'min:1',
                'max:50',
                // Rule::unique('buyers', 'phone_number')
                //     ->where('created_by', $user->id)
                //     ->where('is_external', 1)
                //                    ->where('is_verified', 1)
            ], // if not verified, must update infos

            // 'password' => [
            //     Rule::requiredIf(function () {
            //         return !filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
            //     }),
            //     'confirmed'
            // ],
            // 'password_confirmation' => [
            //     Rule::requiredIf(function () {
            //         return !filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
            //     })
            // ],

            'auth_code' => [
                Rule::requiredIf(function () {
                    return filter_var(request('mpesa_mini_app'), FILTER_VALIDATE_BOOLEAN);
                }),
                // Rule::unique('buyers', 'auth_code')
                //     ->where('created_by', $user->id)
                //     ->where('is_external', 1)
            ],
            'mpesa_mini_app' => ['nullable', 'boolean'],


            // address
            'delivery_address' => ['nullable', 'string', 'min:1', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'county_id' => [
                'nullable',
                Rule::exists('states', 'id')
                    ->where('country_id', $this->country_id)
            ],
            'city_id' => [
                'nullable',
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

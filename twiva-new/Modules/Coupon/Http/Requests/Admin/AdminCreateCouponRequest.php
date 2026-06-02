<?php

namespace Modules\Coupon\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateCouponRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => filter_var($this->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'code' => ['required', 'unique:coupons,code', 'min:8', 'max:255'],
            'max_usage' => ['nullable', 'numeric', 'min:1'],
            'type' => ['required', 'string', 'in:fixed,percentage'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expiry_at' => ['nullable', 'date', 'after:today'],
            'is_active' => ['required', 'boolean'],
        ];

        if ($this->input('type') === 'percentage') {
            $rules['amount'][] = 'max:100';
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

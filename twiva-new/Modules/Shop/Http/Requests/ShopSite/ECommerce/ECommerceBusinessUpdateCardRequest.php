<?php

namespace Modules\Shop\Http\Requests\ShopSite\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Entities\User;

class ECommerceBusinessUpdateCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'increase' => ['nullable', 'boolean'],
            'color_id' => ['nullable', 'string'],
            'size_id' => ['nullable', 'string'],
            'shop_domain' => ['nullable', 'string'],
            'increase_multiple' => ['nullable', 'integer', 'min:1'],
            'user_type' => ['required', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

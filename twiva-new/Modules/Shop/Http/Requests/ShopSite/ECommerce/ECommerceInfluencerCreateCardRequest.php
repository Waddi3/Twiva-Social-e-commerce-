<?php

namespace Modules\Shop\Http\Requests\ShopSite\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Entities\User;

class ECommerceInfluencerCreateCardRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'product_id' => ['required', 'exists:influencer_shop_product,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color_id' => ['nullable', 'string'],
            'size_id' => ['nullable', 'string'],
            'should_redirect' => ['nullable', 'bool'],
            'shop_domain' => ['nullable', 'string'],
            'user_type' => ['required', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

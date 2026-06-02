<?php

namespace Modules\Shop\Http\Requests\ShopSite\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Entities\User;

class ECommerceBusinessDeleteCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:influencer_shop_product,id'],
            'user_type' => ['required', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType]

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

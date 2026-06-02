<?php

namespace Modules\Shop\Http\Requests\ShopSite\InfluencerSite;

use Illuminate\Foundation\Http\FormRequest;

class CreateCardRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'product_id' => ['required', 'exists:influencer_shop_product,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color_id' => ['nullable', 'string'],
            'size_id' => ['nullable', 'string'],
            'should_redirect' => ['nullable', 'bool'],
            'shop_domain' => ['nullable', 'string','exists:shops,domain']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
<?php

namespace Modules\Shop\Http\Requests\ShopSite\InfluencerSite;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:influencer_shop_product,id'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
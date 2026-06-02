<?php

namespace Modules\Shop\Http\Requests\ShopSite\InfluencerSite;

use Illuminate\Foundation\Http\FormRequest;

class ManageCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => ['required', 'exists:shopping_cart,id'],
            'increase' => ['required', 'boolean']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
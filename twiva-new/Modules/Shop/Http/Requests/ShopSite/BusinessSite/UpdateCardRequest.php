<?php

namespace Modules\Shop\Http\Requests\ShopSite\BusinessSite;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

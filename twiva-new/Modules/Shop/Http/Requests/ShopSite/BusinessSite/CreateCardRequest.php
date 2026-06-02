<?php

namespace Modules\Shop\Http\Requests\ShopSite\BusinessSite;

use Illuminate\Foundation\Http\FormRequest;

class CreateCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color_id' => ['nullable', 'string'],
            'size_id' => ['nullable', 'string'],
            'should_redirect' => ['nullable', 'bool'],
            'shop_domain' => ['nullable', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
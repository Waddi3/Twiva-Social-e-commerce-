<?php

namespace Modules\Shop\Http\Requests\ShopSite\BusinessSite;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

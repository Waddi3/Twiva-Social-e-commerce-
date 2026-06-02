<?php

namespace Modules\Products\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\Product;

class DeleteProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
        ];
    }

    public function authorize(): bool
    {
        $user_products_id = Product::where('user_id', auth('api')->id())->pluck('id')->toArray();
        return in_array($this->product_id, $user_products_id);
    }
}

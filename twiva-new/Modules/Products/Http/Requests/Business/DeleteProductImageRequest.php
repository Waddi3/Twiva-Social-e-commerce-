<?php

namespace Modules\Products\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\ProductImage;

class DeleteProductImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'image_id' => ['required', Rule::in(ProductImage::select('id', 'product_id')->where('product_id', $this->product_id)->pluck('id')->toArray())]
        ];
    }

    public function authorize(): bool
    {
        $user_products_id = Product::where('user_id', auth('api')->id())->pluck('id')->toArray();
        return in_array($this->product_id, $user_products_id);
    }
}

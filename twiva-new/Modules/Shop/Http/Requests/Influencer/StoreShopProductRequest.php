<?php

namespace Modules\Shop\Http\Requests\Influencer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\InfluencerShopProducts;

class StoreShopProductRequest extends FormRequest
{

    public function messages()
    {
        return [
            'not_in' => 'You Already Have This product',
        ];
    }

    public function rules(): array
    {
        $influencer_products = InfluencerShopProducts::where('influencer_id', auth('api')->id())->pluck('product_id')->toArray();
        $influencer_products = implode(',', $influencer_products);
        return [
            'products' => ['array', 'required'],
            'products.*.product_id' => ['required', 'exists:products,id', "not_in:$influencer_products.*"],
            'products.*.product_title' => ['required', 'string'],
            // 'products.*.product_price' => ['required', 'numeric'],      /*TODO: Fix min price*/
            'products.*.product_description' => ['required', 'string'],
            'products.*.main_product_image' => ['nullable', 'image'],
            'products.*.product_images' => ['nullable'],
            'products.*.product_images.*' => ['required', 'image'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

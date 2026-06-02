<?php

namespace Modules\Shop\Http\Requests\Influencer;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\InfluencerShopProducts;

class UpdateShopProductRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:influencer_shop_product,id'],
            'product_title' => ['required', 'string'],
            // 'product_price' => ['required', 'numeric'],      /*TODO: Fix min price*/
            'product_description' => ['required', 'string'],
            'product_images' => ['nullable'],
            'product_images.*' => ['required', 'image'],
            'main_product_image' => ['nullable', 'image'],
        ];
    }

    public function authorize(): bool
    {
        $influencer_products = InfluencerShopProducts::where('influencer_id', auth('api')->id())->get()->pluck('id')->toArray();
        return in_array($this->id, $influencer_products);
    }
}

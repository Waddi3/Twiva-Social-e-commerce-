<?php

namespace Modules\Buyer\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Entities\User;

class ApiCustomerExpressRequest extends FormRequest
{
    public function messages()
    {
        return [
            'products.*.quantity' => 'QTY need to me more than 0',
            'products.*.id' => 'Invalid Product Selected',

        ];
    }

    public function rules()
    {
        $data = [
            'buyer_id' => ['required', 'exists:buyers,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.quantity' => ['required', 'numeric', 'min:1', 'max:1000'],
        ];
        $data['products.*.id'] = auth('api')->user()->type == User::InfluencerType
            ? ['required', Rule::exists('influencer_shop_product', 'id')->where('influencer_id', auth('api')->id())]
            : ['required', Rule::exists('products', 'id')->where('user_id', auth('api')->id())];

        return $data;
    }
}

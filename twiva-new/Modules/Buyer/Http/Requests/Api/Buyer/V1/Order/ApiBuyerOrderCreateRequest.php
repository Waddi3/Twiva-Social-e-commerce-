<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiBuyerOrderCreateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            // cart of products with color, size and quantity
            'cart' => ['required', 'array'],
            'cart.*' => ['required', 'array'],
            'cart.*.product_id' => ['required'],
            'cart.*.quantity' => ['required', 'numeric', 'min:1'],
            'cart.*.color_id' => ['nullable', 'exists:colors,id'],
            'cart.*.size_id' => ['nullable', 'exists:sizes,id'],
            // address of user to send products
            'address_id' => ['required', Rule::exists('shipping_address', 'id')->where('buyer_id', auth('buyer-api')->id())], // 'exists:shipping_address,id'
            'delivery_instructions' => ['nullable', 'string'],
            'payment_type' => ['required', 'string', 'in:MPESA,PayOnDelivery'],
            'partner_id' => ['nullable', 'integer'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            'update_coupon_info' => ['nullable', 'boolean'],
        ];
    }
}

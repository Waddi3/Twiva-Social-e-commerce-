<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1\Coupon;

use Modules\Buyer\Http\Requests\Api\Buyer\V1\Order\ApiBuyerOrderCreateRequest;
use Modules\Buyer\Repositories\Order\ApiBuyerOrderRepository;
use Modules\Coupon\Entities\Coupon;

class ApiBuyerCouponController
{

    public function __construct(public ApiBuyerOrderRepository $api_buyer_order_repository) {}

    public function checkCoupon(ApiBuyerOrderCreateRequest $request, $account = null)
    {
        $is_validate = false;
        $coupon_discount = 0;
        $buyer = auth('buyer-api')->user();

        $validated = $request->validated();
        $coupon_code = $validated['coupon_code'];

        $ttl = now()->addMinutes(10);
        $key = "coupon-$coupon_code"; // -{$buyer?->id}

        $product_ids = $this->api_buyer_order_repository->getProductIds($validated);
        $products = $this->api_buyer_order_repository->getProducts($validated);

        ['order_amount' => $order_amount, 'shipping_amount' => $shipping_amount] = $this->api_buyer_order_repository->calculateOrderAmount($validated);

        $total_with_shipping_fees = $order_amount + $shipping_amount;

        if (array_key_exists('update_coupon_info', $validated) && $validated['update_coupon_info']) {
            cache()->forget($key);
        }

        if (is_null(cache()->get($key))) {
            ['coupon' => $coupon, 'buyer' => $buyer] = cache()->remember($key, $ttl, function () use ($coupon_code, $buyer, $total_with_shipping_fees) {
                return [
                    'coupon' => Coupon::validateCouponForBuyer($coupon_code, $buyer, $total_with_shipping_fees),
                    'buyer' => $buyer,
                ];
            });
            if ($coupon) {
                if (isset($total_with_shipping_fees)) {
                    $coupon_discount = $coupon->calculateDiscount($total_with_shipping_fees);
                }
                $is_validate = true;
            } else {
                cache()->forget($key);
            }
        }

        $error_message = '';
        if (!$is_validate) {
            $error_message = Coupon::validateMessageCouponForBuyer($coupon_code, $buyer, $total_with_shipping_fees);
        }
        return response()->json([
            'is_validate' => $is_validate,
            'error_message' => $error_message,
            'coupon_code' => $coupon_code,
            'total_with_shipping_fees' => $total_with_shipping_fees,
            'coupon_with_shipping_fees' => $total_with_shipping_fees - $coupon_discount,
            'coupon_discount' => $coupon_discount
        ]);
    }
}

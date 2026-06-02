<?php

namespace Modules\Order\Traits;

use Illuminate\Support\Facades\Session;
use Modules\Coupon\Entities\CouponBuyer;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;

trait CreateOrderTrait {

    public static function createOrders($productFromSession, $products_grouped_by_business, $e_commerce = 0)
    {
        $newOrders = [];
        $shippingFees_all = 0;
        $count_of_all_orders = 0;
        foreach ($products_grouped_by_business as $business_id => $influencer_products)
        {
            $productsByBusinesses = $influencer_products->groupBy('shop_id');
            $count_of_all_orders += count($productsByBusinesses);
        }

        foreach ($products_grouped_by_business as $business_id => $influencer_products)
        {
            $addressId = Session::get('address_id');
            $address = ShippingAddress::find($addressId);
            $partnerId = Session::get('partner_id');
            $shippingFees_business = ShippingService::calculateShippingFee($influencer_products, $address?->city_id, $partnerId);

            $count_of_shops = $influencer_products->groupBy('shop_id')->count();
            $shippingFees_shop = $shippingFees_business / $count_of_shops;
            $shippingFees_all += $shippingFees_business;

            foreach ($influencer_products->groupBy('shop_id') as $shop_id => $shop_products)
            {
                $order_amount = 0;
                $order = Order::create([
                    'buyer_id'              => auth('buyer')->id() ?? null,
                    'shop_id'               => $shop_id,
                    'partner_id'            => Session::has('partner_id') ? Session::get('partner_id') : null,
                    'business_id'           => $business_id,
                    'address_id'            => Session::get('address_id'),
                    'order_status'          => Order::WAITING_FOR_PAYMENT_STATUS,
                    'order_amount'          => $order_amount,
                    'payment_status'        => Order::NOT_PAID,
                    'delivery_instructions' => session()->get('delivery_instructions'),
                    'e_commerce'            => $e_commerce,
                ]);
                $newOrders[] = $order;

                foreach ($shop_products as $product)
                {
                    if ($product instanceof Product)
                    {
                        $product_price = $product->price;
                        $product_retail_price = $product->retail_price;
                        $product_discount_price = $product->discount;
                    }
                    else
                    {
                        $product_price = $product->productDetails->price;
                        $product_retail_price = $product->productDetails->retail_price;
                        $product_discount_price = $product->productDetails->discount;
                    }
                    $orderDetails = OrderDetails::create(
                        [
                            'order_id'               => $order->id,
                            'product_id'             => $product->id,
                            'product_type'           => $product->getMorphClass(),
                            'amount'                 => $product->product_price,
                            'product_price'          => $product_price,
                            'product_retail_price'   => $product_retail_price,
                            'product_discount_price' => $product_discount_price,
                            'quantity'               => $productFromSession['buyerProducts'][$product->id]['quantity'],
                            'size'                   => $productFromSession['buyerProducts'][$product->id]['size_id'] ?? null,
                            'color'                  => $productFromSession['buyerProducts'][$product->id]['color_id'] ?? null,
                            'e_commerce'             => $e_commerce,
                        ]
                    );
                    $order_amount += $orderDetails->quantity * $product->product_price;
                }

                $coupon_buyer = session('coupon_buyer');
                $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $order_amount, $shippingFees_shop, $count_of_all_orders);
                $order->update(['order_amount' => round($order_amount), 'shipping_amount' => $shippingFees_shop, 'coupon_discount' => $coupon_discount]);
            }
        }
        return $newOrders;
    }
}

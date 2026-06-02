<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Order\Entities\Order;

class CouponBuyer extends Model
{
    protected $fillable = [
        'coupon_id',
        'buyer_id',
        'amount',
        'type',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'coupon_orders', 'coupon_buyer_id', 'order_id')->withTimestamps();
    }

    public static function relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $amount, $shipping_amount, $order_count)
    {
        $coupon_discount = 0;
        if ($coupon_buyer) {
            $all_amount = $amount + $shipping_amount;
            $coupon_discount = $coupon_buyer->coupon->calculateDiscount($all_amount);
            if ($coupon_buyer->coupon->type == Coupon::TYPE_FIXED) {
                $coupon_discount /= $order_count;
            }
            $coupon_buyer->orders()->syncWithoutDetaching([$order->id]);
        }
        return $coupon_discount;
    }
}

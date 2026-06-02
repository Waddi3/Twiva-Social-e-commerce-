<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Model;

class CouponOrder extends Model
{
    protected $fillable = [
        'coupon_buyer_id',
        'order_id',
    ];
}

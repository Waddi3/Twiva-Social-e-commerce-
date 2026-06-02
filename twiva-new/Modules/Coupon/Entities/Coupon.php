<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Modules\Buyer\Entities\Buyer;
use Modules\Order\Entities\Order;

use function Clue\StreamFilter\fun;

class Coupon extends Model
{
    use SoftDeletes;

    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPES = [
        self::TYPE_FIXED => self::TYPE_FIXED,
        self::TYPE_PERCENTAGE => self::TYPE_PERCENTAGE
    ];

    protected $fillable = [
        'id',
        'name',
        'code', // Unique
        'max_usage', // Optional field
        'type', // ['fixed', 'percentage']  // Enum type for fixed or percentage
        'amount', // Decimal for precise amount decimal(10, 2)
        'expiry_at', // Timestamp for expiration date, nullable in case of no expiry
        'is_active',
        'bypass_the_minimum',
        'by_spin_wheel',
        'buyer_id',
    ];

    public $casts = [
        'expiry_at' => 'datetime'
    ];


    public function formatExpiryAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this?->expiry_at?->format('d/m/Y');
            },
        );
    }

    public function percentageAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->type == self::TYPE_PERCENTAGE ? ($this->amount / 100) : $this->amount;
            },
        );
    }


    /**
     * Generate a random code
     *
     * @return string
     */
    public function generateRandomCode($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomCode;
    }

    /**
     * Generate a unique code
     *
     * @return string
     */
    public static function generateUniqueCode($length = 8)
    {
        do {
            $code = (new self)->generateRandomCode($length);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    // public function orders()
    // {
    //     return $this->hasManyThrough(Order::class, CouponOrder::class, 'coupon_id', 'id', 'id', 'order_ids');
    // }


    public function orders()
    {
        return $this->belongsToMany(CouponOrder::class, Order::class, 'id', 'id', 'coupon_id', 'order_ids');
    }

    public function couponBuyers()
    {
        return $this->hasMany(CouponBuyer::class);
    }

    // public function couponOrders()
    // {
    //     return $this->hasMany(CouponOrder::class);
    // }

    public function scopeValidateExpiryAt($query)
    {
        return $query->where(function ($where) {
            return $where->whereNull('expiry_at')->orWhereDate('expiry_at', '>=', Carbon::now());
        });
    }


    public function scopeValidateMaxUsage($query)
    {
        return $query->withCount('couponBuyers as all_use')
            ->havingRaw('CASE
                WHEN ISNULL(coupons.max_usage) THEN 1
                WHEN coupons.max_usage = 0  THEN 1
                ELSE all_use <= coupons.max_usage
            END');
    }

    public function scopeActive($query, $is_active = true)
    {
        return $query->where('is_active', $is_active);
    }


    public function scopeNotUse($query, $buyer)
    {
        return $query->withCount(['couponBuyers' => function ($with_count) use ($buyer) {
            $with_count->where('buyer_id', $buyer?->id);
        }])
            ->having('coupon_buyers_count', 0);
    }


    public function scopeValidateRatePriceCoupon($query, $total_with_shipping_fees)
    {
        $times = config('twiva.spin-wheel.value-of-the-total-order-should-be-n-times-the-coupon-value');
        return $query->whereRaw("
            CASE
                WHEN coupons.type = 'fixed'  and coupons.by_spin_wheel = 1 THEN coupons.amount *  $times  <= $total_with_shipping_fees
                WHEN coupons.type = 'fixed'  and coupons.bypass_the_minimum = 0 THEN coupons.amount *  $times  <= $total_with_shipping_fees
                ELSE 1 = 1
            END
        ")
            // ->addSelect(DB::raw("coupons.amount *  $times as multiple, coupons.amount *  $times  <= $total_with_shipping_fees as con, $total_with_shipping_fees as ttt"))
            // ->dd()
        ;
        // ELSE 1 = 1 -- This can be used as a default case, it essentially means no filtering for the else part
        // ELSE 0 -- This ensures no rows are returned if none of the conditions match
    }

    public static function validateCouponForBuyer($code, $buyer, $total_with_shipping_fees)
    {
        return self::where('code', $code)
            ->notUse($buyer)
            ->validateExpiryAt()
            ->active()
            ->validateMaxUsage()
            ->validateRatePriceCoupon($total_with_shipping_fees)
            //            ->exists();
            ->first();
        //            ->dd();
    }

    public function calculateDiscount($total)
    {
        $amount = 0;
        if ($this->type == self::TYPE_FIXED) {
            $amount = $this->amount;
        } else {
            $amount = $total * $this->percentage_amount;
        }

        return $amount;
    }

    public function buyers()
    {
        return $this->belongsToMany(Buyer::class, 'coupon_buyers')->withPivot('amount')->withTimestamps();
    }

    public function scopeForAdmin($query)
    {
        return $query->where('by_spin_wheel', 0)->whereNull('buyer_id');
    }

    public static function spinWheelBuyerToday()
    {
        $buyer_id = auth('buyer')->id();
        if ($buyer_id) {
            return self::whereDate('created_at', Carbon::today())
                ->where('by_spin_wheel', 1)
                ->where('buyer_id', $buyer_id)
                ->count();
        }
        return session('try-spin-wheel-today');
    }


    public static function getChanceWith60TryAgain($start = 0, $end = 2000, $step = 100)
    {
        $percent_of_get_discount = config('twiva.spin-wheel.winner-percent');
        $percent_of_get_discount = 40;
        $range = range($start, $end, $step);
        $range_len = count($range) - 1;
        $repeated_try_again = ceil(($range_len * 100) / $percent_of_get_discount) - $range_len;
        $try_again_repeated = array_fill(0, max($repeated_try_again - 1, 0), 0);
        $spin_wheel = array_merge($range, $try_again_repeated);
        // dd($range, $range_len, (($range_len * 100) / 40), $repeated_try_again, $try_again_repeated, $spin_wheel);
        return $spin_wheel;
    }

    public static function choseRandomElement($spin_wheel)
    {
        shuffle($spin_wheel);
        $result = Arr::random($spin_wheel);
        return $result;
    }

    public static function getIndexFromRandomElement($get_element, $spin_wheel)
    {
        return array_search($get_element, $spin_wheel);
    }

    public static function validateMessageCouponForBuyer($code, $buyer, $total_with_shipping_fees)
    {
        $message_error = 'Please check you coupon code';
        $coupon = self::where('code', $code)
            ->UsedBy($buyer)
            ->withCount('couponBuyers as all_use')
            // ->ExpiryAt()
            // ->active()
            // ->validateMaxUsage()
            ->ratePriceCoupon()
            ->first();

        if ($coupon) {
            if ($coupon->coupon_buyers_count) {
                $message_error = "You've used this coupon before.";
            } elseif ($coupon->max_usage && $coupon->all_use >= $coupon->max_usage) {
                $message_error = "This coupon has reached its max usage.";
            } elseif ($coupon->expiry_at &&  ($coupon->expiry_at < Carbon::now())) {
                $message_error = "This coupon has expired.";
            } elseif ($coupon->type = 'fixed'  && $coupon->by_spin_wheel == 1 && $coupon->n_time_price > $total_with_shipping_fees) {
                $message_error = "Order must be above ". (int)$coupon->n_time_price ." KSH";
            } elseif (!$coupon->active) {
                $message_error = "This coupon is not active.";
            }
        }
        return $message_error;
    }

    public function scopeUsedBy($query, $buyer)
    {
        return $query->withCount(['couponBuyers' => function ($with_count) use ($buyer) {
            $with_count->where('buyer_id', $buyer?->id);
        }]);
    }

    public function scopeRatePriceCoupon($query)
    {
        $times = config('twiva.spin-wheel.value-of-the-total-order-should-be-n-times-the-coupon-value');
        return $query->selectRaw("(coupons.amount *  $times) as n_time_price");
    }
}

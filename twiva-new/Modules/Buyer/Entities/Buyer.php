<?php

namespace Modules\Buyer\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Modules\Coupon\Entities\Coupon;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\ShippingAddress;
use Modules\User\Entities\Otp;

class Buyer extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    const BuyerType = 'Buyer';


    const INDIVIDUAL = 'INDIVIDUAL';
    const RETAILER = 'RETAILER';

    const BUYER_CLASSES = [
        self::INDIVIDUAL => 'INDIVIDUAL',
        self::RETAILER => 'RETAILER',
    ];
    protected $casts = [
        'cart' => 'json'
    ];
    protected $guarded = [];

    public function addresses()
    {
        return $this->hasMany(ShippingAddress::class, 'buyer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function OTP(): HasOne
    {
        return $this->hasOne(BuyerOTP::class);
    }

    public function getOrdersAmountAttribute()
    {
        return $this->orders->sum('order_amount');
    }

    public function getOrdersCountAttribute()
    {
        return $this->orders->count();
    }

    public function getTypeAttribute()
    {
        return self::BuyerType;
    }

    public function getMobileAttribute()
    {
        return $this->phone_number ? "+$this->phone_code$this->phone_number" : null;
    }

    public function getRepMobileAttribute()
    {
        return $this->rep_phone_number ? "+$this->rep_phone_code$this->rep_phone_number" : null;
    }

    public static function splitName(mixed $name)
    {
        return explode(' ', $name, 2);
    }

    public static function splitPhoneNumber(mixed $phone_number, mixed $phone_code): array
    {
        $code = Str::replace('+', '', $phone_code);
        $phone_number = self::removeSpace($phone_number);
        $mobile = "{$code}-{$phone_number}";
        return [$phone_number, $phone_code, $mobile];
    }

    public static function removeSpace($phone_number)
    {
        return Str::replace(' ', '', $phone_number);
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

    public static function existsInnerBuyer($column, $value)
    {
        return Buyer::where($column, $value)->whereNull('created_by')->where('is_external', 0)->exists();
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_buyers')->withPivot('amount')->withTimestamps();
    }

    public function routeNotificationForOnfon($notification)
    {
        return $this->mobile;
    }

}

<?php

namespace Modules\Buyer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerOTP extends Model
{
    protected $table = 'buyers_otp';
    protected $fillable = ['code'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function buyer(): BelongsTo {
        return $this->belongsTo(Buyer::class);
    }

    public static function generateOtpCode($number_digit = 6)
    {
        return mt_rand(pow(10, $number_digit - 1), pow(10, $number_digit) - 1);
    }
}

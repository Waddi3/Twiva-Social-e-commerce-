<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class WinningPayout extends Model
{
    //    TODO::H write job to handle not solved payment or fail payment with init status
    const STATUS_INIT = 'init';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';
    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS = [
        self::STATUS_INIT => 'init',
        self::STATUS_SUCCESS => 'success',
        self::STATUS_FAIL => 'fail',
        self::STATUS_IN_PROGRESS => 'in_progress',
    ];
    protected $fillable = [
        'user_id',
        'request_type',
        'phone_number',
        'till_number',
        'originator_conversation_id',
        'transaction_id',
        'transaction_amount',
        'status',
        'payment_gateway_type',
        'transaction_response',
        'transaction_details',
    ];

    protected $casts = [
        'transaction_response' => 'collection'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobile(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (Str::startsWith($this->phone_number, '0')) {
                    return "254" . Str::after($this->phone_number, '0');
                }elseif(!Str::startsWith($this->phone_number, '0') && Str::length($this->phone_number) < 11){
                    return "254$this->phone_number";
                }
                return $this->phone_number;
            }
        );
    }
}

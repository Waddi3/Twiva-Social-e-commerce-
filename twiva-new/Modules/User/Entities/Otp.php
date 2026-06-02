<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $guarded = [];
    protected $table = 'otp';
    protected $casts = ['expiry_date' => 'datetime'];
    const FOR_MOBILE = 'MOBILE';
    const FOR_EMAIL = 'EMAIL';
    const TWILIO_VERIFY = 'TWILIO_VERIFY';


}

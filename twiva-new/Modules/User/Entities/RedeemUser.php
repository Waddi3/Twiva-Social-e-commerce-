<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class RedeemUser extends Model
{
    const ACCEPT_STATUS = 'ACCEPTED';

    const STATUS = [
        self::ACCEPT_STATUS => self::ACCEPT_STATUS,
    ];
    protected $fillable = [
        'user_id',
        'points',
        'status',
    ];
}

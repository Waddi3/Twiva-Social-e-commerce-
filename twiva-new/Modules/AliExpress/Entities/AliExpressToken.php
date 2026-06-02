<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressToken extends Model
{
    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'response',
    ];

    protected $casts = [
        'response' => 'json',
    ];
}

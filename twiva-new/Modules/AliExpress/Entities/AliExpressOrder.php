<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressOrder extends Model
{

    protected $fillable = [
        'order_id',
        'order_list',
        'parameters',
        'response',
    ];

    protected $casts = [
        'order_list' => 'json',
        'parameters' => 'json',
        'response' => 'json',
    ];
}

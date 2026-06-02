<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;

class TentativeDeliveryPeriod extends Model
{

    protected $fillable = [
        'days'
    ];
}

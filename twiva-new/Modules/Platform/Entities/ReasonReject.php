<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Model;

class ReasonReject extends Model
{
    protected $fillable = [
        'reason',
        'order',
    ];

}

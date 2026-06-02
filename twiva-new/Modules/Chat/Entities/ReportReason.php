<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;

class ReportReason extends Model
{
    protected $fillable = [
        'reason',
        'order'
    ];
}

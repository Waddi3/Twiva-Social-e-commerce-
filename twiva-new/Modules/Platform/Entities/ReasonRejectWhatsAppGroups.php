<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Model;

class ReasonRejectWhatsAppGroups extends Model
{
    protected $fillable = [
        'reason_reject_id',
        'whats_app_group_id',
    ];
}

<?php

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Model;

class NotificationTitle extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
    ];
}

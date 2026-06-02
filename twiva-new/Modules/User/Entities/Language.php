<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;


class Language extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];
}

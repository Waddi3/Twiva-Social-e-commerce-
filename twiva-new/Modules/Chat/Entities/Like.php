<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'timeline_id'];
}

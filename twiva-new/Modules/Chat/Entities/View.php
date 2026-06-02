<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['user_id', 'timeline_id'];
}

<?php

namespace Modules\Home\Entities;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $table = 'states';
    protected $id = 'id';
    protected $fillable = ['name', 'country_id'];
}

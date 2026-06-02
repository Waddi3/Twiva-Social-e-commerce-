<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;

class Colors extends Model
{
    //
    protected $table = 'colors';
    protected $id = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'hexaval'];
}

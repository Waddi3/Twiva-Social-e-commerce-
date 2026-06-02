<?php

namespace Modules\Home\Entities;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    protected $table = 'countries';
    protected $id = 'id';

    protected $fillable = ['name', 'currency'];
}

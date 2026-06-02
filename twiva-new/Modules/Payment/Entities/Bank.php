<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //
    protected $table = 'banks';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $fillable = ['bank_name'];
}

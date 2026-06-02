<?php

namespace Modules\System\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class System extends Model
{
    protected $table = 'system';
    public $timestamps = false;
    protected $guarded = [];
}

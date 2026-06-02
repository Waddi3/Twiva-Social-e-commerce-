<?php

namespace Modules\Home\Entities;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    protected $table = 'cities';
    protected $fillable = ['name', 'state_id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

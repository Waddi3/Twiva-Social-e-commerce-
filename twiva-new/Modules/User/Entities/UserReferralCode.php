<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class UserReferralCode extends Model
{

    protected $guarded = [];

    public function invitor()
    {
        return $this->belongsTo(User::class, 'invitor_id');
    }

    public function invited()
    {
        return $this->belongsTo(User::class, 'invited_id');
    }
}

<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;

class VerifyData extends Model
{
    //
    protected $table = 'verifydata';
    protected $id = 'id';
    protected $fillable = ['userID', 'verifydata', 'review'];
}

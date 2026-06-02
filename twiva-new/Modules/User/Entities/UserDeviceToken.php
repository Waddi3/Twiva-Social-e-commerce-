<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    protected $table = 'user_device_token';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $dates = ['created_at','updated_at'];
    protected $fillable = ['user_id','device_token','device_type'];
}

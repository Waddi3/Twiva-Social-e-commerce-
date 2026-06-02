<?php

namespace Modules\Admin\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_user';

    protected $fillable = [
        'name','username','email', 'password'
    ];

    public function getAuthPassword()
    {
      return $this->password;
    }
}

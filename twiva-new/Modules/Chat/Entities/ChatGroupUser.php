<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;

class ChatGroupUser extends Model
{
    protected $fillable = ['user_id', 'chat_group_id'];

}

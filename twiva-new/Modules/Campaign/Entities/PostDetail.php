<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class PostDetail extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'text_one', 'text_two', 'text_three', 'post_user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

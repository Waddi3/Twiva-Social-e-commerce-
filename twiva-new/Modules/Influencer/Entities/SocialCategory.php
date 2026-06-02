<?php

namespace Modules\Influencer\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SocialCategory extends Model
{

    public $timestamps = false;

    
    protected $guarded = [];

    public function influencers()
    {
        return $this->belongsToMany(User::class,'influencers_social_categories');
    }
}

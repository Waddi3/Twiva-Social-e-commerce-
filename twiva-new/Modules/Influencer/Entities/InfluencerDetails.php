<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Home\Entities\Category;
use Modules\User\Entities\User;

class InfluencerDetails extends Model
{
    use SoftDeletes;

    protected $table = "influencer_details";
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $guarded = [];


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'influencer_id')->withTrashed();
    }

    public function influencerInterests()
    {
        $this->belongsToMany(Category::class, 'influencer_interests', 'user_id', 'influencer_id');
    }


}

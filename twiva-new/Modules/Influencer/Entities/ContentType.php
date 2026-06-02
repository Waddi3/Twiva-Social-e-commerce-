<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{


    const CHALLENGES_ID = 1;
    const LIVE_ID = 2;
    const POSTS_ID = 3;
    const REELS_ID = 4;
    const STORIES_ID = 5;
    const VIDEOS_ID = 6;

    public $timestamps = false;

    protected $fillable = [];


}

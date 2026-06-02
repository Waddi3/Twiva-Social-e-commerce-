<?php

namespace Modules\Influencer\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static updateOrCreate(array $array, array $array1)
 */
class SocialEngagements extends Model
{
    protected $casts = [
        'facebook_gender' => 'array',
        'facebook_countries' => 'array',
        'youtube_gender' => 'array',
        'youtube_countries' => 'array',
    ];

    protected $guarded = [];

    public function user()
    {
        $this->belongsTo(User::class);
    }

}

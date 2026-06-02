<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class Platform extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    const FacebookID = 1;
    const InstagramID = 2;
    const TwitterID = 3;
    const YoutubeID = 4;
    const TikTokID = 5;
    const AllowedToPostPlatform = [self::FacebookID, self::InstagramID, self::TwitterID, self::TikTokID];

    public static function getPlatformName($id)
    {
        return match ($id) {
            self::FacebookID => 'Facebook',
            self::InstagramID => 'Instagram',
            self::TwitterID => 'X',
            self::YoutubeID => 'Youtube',
            self::TikTokID => 'Tiktok',
            default => "",
        };
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_platforms');
    }
}

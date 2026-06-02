<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
    protected $fillable = ['user_id', 'platform_id', 'social_id', 'token', 'name', 'additional_info'];
    protected $guarded = [];
    protected $with = ['platformName'];
    protected $casts = ['additional_info' => 'array'];
    protected $appends = ['socialLink'];

    public function platformName()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function getStatusAttribute()
    {
        return $this->token ? 'Connected' : 'Reconnect';
    }

    const Tires = [
        'Nano' => [1000, 10000],
        'Micro' => [10000, 50000],
        'Mid-tier' => [50000, 500000],
        'Macro' => [500000, 1000000],
        'Mega' => [1000000, 5000000],
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function getSocialLinkAttribute()
    {
        if ($this->platform_id == Platform::FacebookID) {
            if (json_decode($this->additional_info)?->username) {
                return $this->platform->link . (json_decode($this->additional_info)?->username);
            }
            return '';
        } else if ($this->platform_id == Platform::InstagramID) {
            return $this->platform->link . $this->name;
        }
        return '';

    }


    public static function addWhatsAppGroups($data)
    {
        $user = auth('api')->user();
        $data[] = [
            "platform" => [
                "id" => 0,
                "name" => "whatsapp"
            ],
            "is_connected" => (bool) $user->whatsappGroups()->count(), // ->verified()
            "canPost" => false,
        ];
        return $data;
    }

    public function username(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->platform_id == Platform::FacebookID || $this->platform_id == Platform::InstagramID) {
                    return $this->name;
                } else if ($this->platform_id == Platform::TwitterID) {
                    return $this->additional_info['nickname'] ?? '';
                } else if ($this->platform_id == Platform::TikTokID) {
                    return $this->additional_info['name'] ?? '';
                }
                return '';
            }
        );
    }
}

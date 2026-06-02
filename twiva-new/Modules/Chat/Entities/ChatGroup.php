<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;

class ChatGroup extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
        'creator_id',
    ];


    public static function pathImage($format = 'Y-m-d')
    {
        return  'chat-groups/' . now()->format($format);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_group_users')->withTimestamps();
    }

    public function addedAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->created_at->format('d M Y, H:i');
            }
        );
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return Storage::disk('public')->url($this->image);
            }
        );
    }
}

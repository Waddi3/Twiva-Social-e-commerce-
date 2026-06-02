<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\User\Entities\User;

class Timeline extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content',
        'image',
        'thumbnail',
        'is_pinned',
        'user_id',
        'chat_group_id',
    ];

    public static function pathTimeline($format = 'Y-m-d')
    {
        return  'timelines/' . now()->format($format);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->image ? Storage::disk('public')->url($this->image) : null;
            }
        );
    }

    public function addedAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->created_at->format('d M Y, H:i');
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

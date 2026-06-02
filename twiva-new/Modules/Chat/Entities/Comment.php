<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Entities\User;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'user_id',
        'timeline_id',
        'reply_id',
    ];

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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'reply_id');
    }
}

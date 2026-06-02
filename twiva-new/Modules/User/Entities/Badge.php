<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'title',
        'background_color',
        'color',
        'icon',
    ];

    public function name(): Attribute
    {
        return Attribute::make(get: fn() => $this->title);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_users')->withPivot(['status', 'comments'])->withTimestamps();
    }
}

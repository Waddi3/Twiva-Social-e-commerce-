<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class BadgeUser extends Model
{

    const STATUSES = [
        'pending' => 'pending',
        'approved' => 'approved',
        'declined' => 'declined',
    ];

    const STATUSES_CLASSES_CSS = [
        'pending' => 'in-process',
        'approved' => 'eshop-active',
        'declined' => 'eshop-failed',
    ];

    protected $fillable = [
        'user_id',
        'badge_id',
        'status', // 'pending', 'approved', 'declined'
        'comments',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function addOn(): Attribute
    {
        return Attribute::make(get: fn() => $this->created_at->format('d M Y, H:i'));
    }

    public function statusClassesCss()
    {
        return self::STATUSES_CLASSES_CSS[$this->status];
    }
}

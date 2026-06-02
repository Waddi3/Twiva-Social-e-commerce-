<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Home\Entities\City;
use Modules\User\Entities\User;

class WhatsAppGroup extends Model
{

    use SoftDeletes;

    public const STATUSES = [
        'pending' => 'pending',
        'verified' => 'verified',
        'rejected' => 'rejected',
    ];
    protected $fillable = [
        'user_id',
        'name',
        'number_of_people',
        'city_id',
        'invite_link',
        'status', // pending | verified | rejected
    ];

    public function scopeVerified($query, $status = 'verified')
    {
        return $query->where('status', $status);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function statusesCssClasses()
    {
        return match ($this->status) {
            'pending' => 'in-process',
            'verified' => 'eshop-active',
            'rejected' => 'eshop-failed',
            default => "in-process",
        };
    }

    public function ActiveVerified()
    {
        return $this->status == self::STATUSES['pending'];
    }

    public function addedOn()
    {
        return $this->created_at->format('d M Y');
    }

    public function categories()
    {
        return $this->belongsToMany(WhatsAppCategory::class, 'category_whats_app_groups', 'whats_app_group_id', 'categorizable_id')->withTimestamps();
    }

    public function reasonRejections()
    {
        return $this->belongsToMany(ReasonReject::class, 'reason_reject_whats_app_groups', 'whats_app_group_id', 'reason_reject_id')->withTimestamps();
    }

    public function CategoryWhatsAppGroups()
    {
        return $this->hasMany(CategoryWhatsAppGroup::class);
    }
}

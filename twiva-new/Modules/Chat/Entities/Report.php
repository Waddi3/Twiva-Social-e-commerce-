<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class Report extends Model
{
    protected $fillable = [
        'status',
        'content',
        'user_id',
        'timeline_id',
        'report_reason_id',
    ];

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportReason()
    {
        return $this->belongsTo(ReportReason::class);
    }

    public function addedAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->created_at->format('d M Y, H:i');
            }
        );
    }
}

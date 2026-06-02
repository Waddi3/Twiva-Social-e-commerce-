<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class ProductVideo extends Model
{
    protected $fillable = [
        'productable_id',
        'productable_type',
        'video',
        'video_with_subtitle',
    ];

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }

    public function videoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this?->video ? Storage::disk('public')->url($this->video) : null;
            }
        );
    }
    public function  videoWithSubtitleUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this?->video_with_subtitle ? Storage::disk('public')->url($this->video_with_subtitle) : null;
            }
        );
    }
}

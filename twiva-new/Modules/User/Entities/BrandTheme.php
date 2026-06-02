<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\City;

class BrandTheme extends Model {
    protected $fillable = [
        'voice',
        'tone',
        'style',
        'structure',
        'location',
        'city_id',
        'latitude',
        'longitude',
        'min_age',
        'max_age',
        'gender',
        'language_ids',
        'interest_ids',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getInterestIdsListAttribute(): array
    {
        return explode(",", $this->interest_ids);
    }
    public function getLanguageIdsListAttribute(): array
    {
        return explode(",", $this->language_ids);
    }
}

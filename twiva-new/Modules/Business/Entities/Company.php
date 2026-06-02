<?php

namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\City;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\State;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;
use Modules\User\Entities\UserSubscription;

class Company extends Model
{
    // use SoftDeletes;

    protected $table = 'comp_details';
    protected $guarded = [];
    protected $casts = [
        'building' => 'string'
    ];
    const IMAGE_PATH = 'images/products';

    protected $appends = [
        'address',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }


    public function city()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function cityDetails()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
    public function stateDetails()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }
    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function getAddressAttribute()
    {
        return ($this->countryDetails()->select('id', 'name')->first()?->name ?? '--') . ', ' . ($this->stateDetails()->select('id', 'name')->first()?->name ?? '--') . ', ' . ($this->cityDetails()->select('id', 'name')->first()?->name ?? '--') .  ', ' . $this->area;
    }
}

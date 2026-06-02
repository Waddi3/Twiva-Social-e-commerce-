<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\Home\Entities\City;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\State;

class ShippingAddress extends Model
{

    use SoftDeletes, Notifiable;

    protected $table = 'shipping_address';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function county()
    {
        return $this->belongsTo(State::class, 'county_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function cityDetails()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function countryDetails()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function countyDetails()
    {
        return $this->belongsTo(State::class, 'county_id', 'id');
    }


    public function routeNotificationForOnfon($notification)
    {
        return $this->mobile;
    }

    public function getContactNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        return $this->delivery_address . ', ' . ($this->countryDetails->name ?? '--') . ', ' . ($this->countyDetails->name ?? '--') . ', ' . ($this->cityDetails->name ?? '--');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }

    public function mobile(): Attribute
    {
        return Attribute::get(fn() => $this->phone_number ? '+' . Str::replace([' ', '-'], '', $this->phone_number) : null);
    }
}

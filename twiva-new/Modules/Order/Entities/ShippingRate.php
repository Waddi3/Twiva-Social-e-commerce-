<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Home\Entities\City;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\State;

class ShippingRate extends Model
{
    protected $table = 'shipping_rates';
    protected $guarded = [];
    protected $fillable = ['source_city_id', 'destination_city_id', 'rate', 'partner_id'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}

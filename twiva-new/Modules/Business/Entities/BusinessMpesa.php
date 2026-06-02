<?php

namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static updateOrCreate(array $array, array $array1)
 */
class BusinessMpesa extends Model
{
    protected $table = 'busi_mpesa';
    protected $guarded = [];
    protected $casts = [
        'name' => 'string',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function mobile(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (Str::startsWith($this->mobileNumber, '0')) {
                    return "254" . Str::after($this->mobileNumber, '0');
                }elseif(!Str::startsWith($this->mobileNumber, '0') && Str::length($this->mobileNumber) < 11){
                    return "254$this->mobileNumber";
                }
                return $this->mobileNumber;
            }
        );
    }
}

<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class InfluencerInterests extends Model
{
//    protected $table = "influencer_interests";

    protected $fillable = [
        "user_id",
        "image"
    ];

//    public function getImageAttribute($value){
//    	return  url("storage/uploads/images/influencer_images").'/'.$value;
//    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url("storage/uploads/images/influencer_images") . '/' . $value,
        );
    }
}

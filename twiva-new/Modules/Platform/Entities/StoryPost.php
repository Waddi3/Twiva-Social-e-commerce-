<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

/**
 * @method static create(array $input)
 */
class StoryPost extends Model
{

    protected $table = "user_story_post";
    const IMAGE_PATH = 'influencer/stories/images';

    const END_AFTER_TYPE_AFTER_DATE = 1;
    const END_AFTER_TYPE_AFTER_COUNT_OF_TIMES = 2;
    const REPEAT_TYPE_DAILY = 1;
    const REPEAT_TYPE_IN_DAYS = 2;


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    /**
     * The attributes that should be mass assignable.
     *
     * @var array
     */

    protected $guarded = [];

    protected $casts =
    [
        'start_date' => 'custom_datetime: Y m d ',
        'start_time' => 'string',
        'end_date' => 'date',
        'on_date' => 'date',
        'canceled_at' => 'date',
        'publish_type' => 'collection',
    ];


    public function productDetails()
    {
        if (auth('api')->user()->type == User::InfluencerType) {
            return $this->hasOne(InfluencerShopProducts::class, 'id', 'product_id')->withTrashed();
        }
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function getPlatformAttribute()
    {
        if ($this->attributes['platform']) {
            $platforms = explode(',', $this->attributes['platform']);
            return Platform::select('id', 'name')->whereIn('id', $platforms)->get();
        }
        return [];
    }


    public function getImagesAttribute()
    {
        if ($this->image) {
            $images = explode(',', $this->image);
            $images = collect($images);
            $images = $images->filter(function ($image) {
                return strlen($image);
            });
            return $images->map(function ($image) {
                if (str_contains($image, 'twiva.co.ke')) {
                    return $image;
                } else {
                    return asset('storage/' . $image);
                }
            });
        } else {
            return [];
        }
    }

    public function hasPlatform($platformID)
    {
        return in_array($platformID, $this->platform->pluck('id')->toArray());
    }

    public function getAbsoluteImagesAttribute()
    {
        if ($this->image) {
            $images = explode(',', $this->image);
            $images = collect($images);
            return $images->filter(function ($image) {
                return strlen($image);
            });
        } else {
            return collect([]);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }

    public function startDateFormat(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->start_date
                    ? Carbon::parse($this->start_date)->format("d M Y, H:i")
                    : null;
            }
        );
    }

    public function postedOn(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->is_schedule
                    ? ($this->end_date
                        ? Carbon::parse($this->end_date)->format("d M Y") . Carbon::parse($this->start_date)->format(", H:i")
                        : Carbon::parse($this->updated_at)->format("d M Y, H:i"))
                    : '-';
            }
        );
    }
}

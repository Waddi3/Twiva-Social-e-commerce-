<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;

class Shops extends Model
{
    use SoftDeletes;

    const COVER_IMAGE_PATH = 'images/products';
    const LOGO_PATH = 'images/products';

    const TEMP_IMAGES_PATH = 'temp/shop';

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $guarded = [];

    protected $appends = [
        'link'
    ];


    public function products()
    {
        return $this->hasMany(InfluencerShopProducts::class, 'shop_id', 'id');
    }

    public function influencerProducts()
    {
        return $this->hasMany(InfluencerShopProducts::class, 'shop_id', 'id');
    }

    public function productImages()
    {
        return $this->hasManyThrough(
            InfluencerShopProductImages::class,
            InfluencerShopProducts::class,
            'shop_id', // Foreign key on InfluencerInfluencerShopProducts table...
            'shop_product_id', // Foreign key on InfluencerShopProductImages table...
            'id', // Local key on influencer_shop table...
            'id' // Local key on shop_products table...
        );
    }

    public static function generateDomain()
    {
        $number = mt_rand(1000000000, 9999999999);
        if (self::domainExists($number)) {
            return self::generateDomain();
        }
        return $number;
    }

    public static function domainExists($number)
    {
        return self::where('domain', $number)->exists();
    }

    public function generateDomainFromShopName($rand_num = '', $is_set_name_user = false)
    {
        if (!is_numeric($this->domain) && $this->domain) {
            $this->domain = str_replace('_', '-', $this->domain);
            return $this->domain;
        }
        $rand_num = Str::lower($rand_num);
        if ($this->shop_name && !$is_set_name_user) {
            $domain = preg_replace(
                '/^([^a-zA-Z0-9])*/',
                '', // remove special characters from first subdomain
                preg_replace(
                    '![_]+!',
                    '-', // remove multi underline character
                    preg_replace(
                        '/[^a-z]/i',
                        '-', // remove another characters
                        preg_replace('/[^\00-\255]+/u', '', $this->shop_name . $rand_num)
                    )
                )
            ); // remove not english characters
            $domain = str_replace('_', '-', $domain);
        } else {
            if ($is_set_name_user) {
                $domain = preg_replace(
                    '/^([^a-zA-Z0-9])*/',
                    '', // remove special characters from first subdomain
                    preg_replace(
                        '![_]+!',
                        '_', // remove multi underline character
                        preg_replace(
                            '/[^a-zA-Z0-9]/i',
                            '_', // remove another characters
                            preg_replace('/[^\00-\255]+/u', '', $rand_num)
                        )
                    )
                );
                $domain = str_replace('_', '-', $domain);
            } else {
                $domain = preg_replace(
                    '/^([^a-zA-Z0-9])*/',
                    '', // remove special characters from first subdomain
                    preg_replace(
                        '![_]+!',
                        '_', // remove multi underline character
                        preg_replace(
                            '/[^a-zA-Z0-9]/i',
                            '_', // remove another characters
                            preg_replace('/[^\00-\255]+/u', '', Str::random(10))
                        )
                    )
                );
                $domain = str_replace('_', '-', $domain);
            }
        }
        if (self::domainExists($domain)) {
            return $this->generateDomainFromShopName($domain . mt_rand(1000, 9999), $is_set_name_user);
        } else {
            $this->domain = Str::lower($domain);
            $this->save();
            return $this->domain;
        }
    }


    public function getCoverImageAttribute()
    {
        if ($this->attributes['shop_cover_image_path']) {
            return asset('storage/images/products/' . $this->shop_cover_image_path);
        }
        return asset('admin/images/Profile.svg');
    }

    public function getLogoImageAttribute()
    {
        if ($this->attributes['shop_logo_path']) {
            return asset('storage/images/products/' . $this->shop_logo_path);
        }
        return asset('admin/images/Profile.svg');
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shop_id');
    }

    public function forInfluencers()
    {
        return $this->belongsToMany(User::class, 'shop_influencers', 'shop_id', 'user_id', 'id', 'id')->withTimestamps();
    }
    public static function getUser($shop_domain)
    {
        return self::select('id', 'user_id')->with('user')->where('domain', $shop_domain)->first()?->user;
    }

    public function link(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->domain . '.' . config('twiva.bases.shop_domain'),
        );
    }
}

<?php

namespace Modules\System\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConstantSystem extends Model
{

    protected $fillable = [
        'name',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array'
    ];

    public static function getBanner()
    {
        $banner = self::where('key', 'banner')->first()?->value;
        return  $banner ? Storage::disk('public')->url($banner) : asset('front/images/ecommerce/laborday.png');
    }

    public static function getUrlBanner()
    {
        $banner = self::where('key', 'url_banner')->first()?->value;
        return  $banner ?? '#';
    }
}

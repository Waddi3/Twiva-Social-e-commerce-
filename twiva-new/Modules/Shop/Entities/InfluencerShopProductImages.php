<?php

namespace Modules\Shop\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class InfluencerShopProductImages extends Model
{
    const INFLUENCER_PRODUCT_IMAGE_PATH = 'images/products';
    protected $primaryKey = 'id';
    protected $fillable = ['shop_product_id', 'image_path', 'created_at', 'updated_at', 'is_cover_pic'];
    protected $hidden = ['created_at', 'updated_at'];

    public function image()
    {
        return Attribute::make(
            get: fn() => $this->image_path,
        );
    }
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/images/products/' . $this->image_path) : '';
    }
}

<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static create(array $array)
 */
class ProductImage extends Model
{
    //
    const PRODUCT_IMAGE_PATH = 'images/products';
    protected $table = 'product_image';

    protected $primaryKey = 'id';

    protected $fillable = ['product_id', 'image', 'created_at', 'updated_at', 'is_cover_pic'];

    protected $hidden = ['created_at', 'updated_at'];

    public function getImageUrlAttribute()
    {
        if($this->image && Str::contains($this->image, 'http')){
            return $this->image;
        }
        return $this->image ? asset('storage/images/products/' . $this->image) : null;
    }
}

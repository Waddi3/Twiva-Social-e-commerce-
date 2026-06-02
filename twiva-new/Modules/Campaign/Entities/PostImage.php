<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    const IMAGEPATH = 'uploads/images/business_images/post_images';
    protected $guarded = [];

    public function getMediaPathAttribute()
    {
        return asset('storage/uploads/images/business_images/post_images/' . $this->media);
    }

    public function Post()
    {
        return $this->belongsTo("App\model\Post");
    }
}

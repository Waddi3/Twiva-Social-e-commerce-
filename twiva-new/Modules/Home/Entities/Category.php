<?php

namespace Modules\Home\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;

class Category extends Model
{
    //
    protected $table = 'categories';
    protected $id = 'id';

    protected $guarded = [];

    public function subcategories()
    {
        return $this->hasMany(SubCategories::class);
    }

    public function scopeActive($query, $is_active = 1)
    {
        return $query->where('is_active', $is_active);
    }

    public function scopeFeatured($query, $is_featured = 1)
    {
        return $query->where('is_featured', $is_featured);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'id');
    }
}

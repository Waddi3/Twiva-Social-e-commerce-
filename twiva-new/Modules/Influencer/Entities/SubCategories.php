<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Platform\Entities\CategoryWhatsAppGroup;

class SubCategories extends Model
{
    use SoftDeletes;
    protected $table = 'sub_categories';
    protected $id = 'id';
    const EventSubCategoryName = 'Events';

    protected $guarded = [];


    public function scopeActive($query, $is_active = 1)
    {
        return $query->where('is_active', $is_active);
    }

    public function scopeFilterByName($query, $name, $is_active = 1)
    {
        return $query->select([
            'id', 'category_id', 'name', 'is_color', 'is_size', 'is_active'
        ])->active($is_active)
        ->withCount('products')
            ->where('name', 'like', "%$name%");
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category', 'id');
    }

    public function categoryWhatsAppGroup()
    {
        return $this->morphOne(CategoryWhatsAppGroup::class, 'categorizable');
    }
}

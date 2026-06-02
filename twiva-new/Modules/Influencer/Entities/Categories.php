<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Platform\Entities\CategoryWhatsAppGroup;

class Categories extends Model
{
    //
    protected $table = 'categories';
    protected $id = 'id';

    protected $fillable = ['name'];

    //subcategories
    public function subcategories(){
        return $this->hasMany(SubCategories::class, 'category_id', 'id');
    }

    public function categoryWhatsAppGroup()
    {
        return $this->morphOne(CategoryWhatsAppGroup::class, 'categorizable');
    }
}

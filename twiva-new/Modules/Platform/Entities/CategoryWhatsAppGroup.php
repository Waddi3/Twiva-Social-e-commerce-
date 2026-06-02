<?php

namespace Modules\Platform\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\Category;

class CategoryWhatsAppGroup extends Model
{
    protected $fillable = [
        'categorizable_id',
        'categorizable_type',
        'whats_app_group_id',
    ];

    public function categorizable()
    {
        return $this->morphTo();
    }
}

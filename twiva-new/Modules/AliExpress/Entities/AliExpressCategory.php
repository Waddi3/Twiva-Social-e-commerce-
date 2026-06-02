<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressCategory extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'ae_category_id',
        'ae_sub_category_id',
    ];
}

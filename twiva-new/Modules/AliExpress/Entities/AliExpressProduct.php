<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressProduct extends Model
{
    protected $fillable = [
        'product_id',
        'first_level_category_id',
        'second_level_category_id',
        'title',
        'product_main_image_url',
        'product_small_image_url',
        'product_info',
    ];

    protected $casts = [
        'product_small_image_url' => 'json',
        'product_info' => 'json',
    ];
}

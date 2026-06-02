<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressIdsProduct extends Model
{

    protected $fillable = [
        'product_id',
        'title',
        'category_ids',
        'score',
        'item_url',
        'item_main_pic',
    ];
}

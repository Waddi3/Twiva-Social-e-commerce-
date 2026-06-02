<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostProduct extends Pivot
{


    protected $fillable = [
        'post_id',
        'product_id',
    ];

}

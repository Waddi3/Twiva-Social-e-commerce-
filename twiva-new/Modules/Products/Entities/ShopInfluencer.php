<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ShopInfluencer extends Pivot
{
    protected $table = 'shop_influencers';

    protected $fillable = [
        'shop_id',
        'user_id',
    ];
}

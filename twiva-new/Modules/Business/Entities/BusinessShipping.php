<?php

namespace Modules\Business\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Entities\User;

class BusinessShipping extends Model
{
    protected $fillable = ['business_id', 'has_shipping_fees', 'shipping_by_partner', 'pay_on_delivery'];
    public function business(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;


class DeliveryIssue extends Model
{
    protected $table = "delivery_issues";
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = ['order_id', 'issue'];

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

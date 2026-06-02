<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderGroupPaymentRequest extends Model {
    protected $fillable = [
        'uuid',
        'amount',
        'cart',
    ];
    protected $casts = [
        'cart' => 'array',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'group_payment_request_id');
    }

    public function buyerOrderGroupPayment(): HasMany
    {
        return $this->hasMany(BuyerOrderGroupPayment::class, 'request_id');
    }

    public function getAllPaidAmountAttribute()
    {
        return $this->buyerOrderGroupPayment?->whereNotNull('paid_at')
            ?->sum('amount') ?? 0;
    }
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->buyerOrderGroupPayment?->whereNotNull('paid_at')
            ?->sum('amount') ?? 0;
    }

}

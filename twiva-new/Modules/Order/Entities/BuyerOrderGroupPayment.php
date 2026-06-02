<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Buyer\Entities\Buyer;

class BuyerOrderGroupPayment extends Model {
    protected $fillable = [
        'buyer_id',
        'request_id',
        'amount',
        'paid_at'
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function orderGroupPaymentRequest(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'request_id');
    }


}

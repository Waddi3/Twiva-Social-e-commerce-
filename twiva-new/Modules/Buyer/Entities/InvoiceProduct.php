<?php

namespace Modules\Buyer\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
    ];

    public function businessProduct()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function influencerProduct()
    {
        return $this->hasOne(InfluencerShopProducts::class, 'id', 'product_id');
    }
}

<?php

namespace Modules\AliExpress\Entities;

use Illuminate\Database\Eloquent\Model;

class AliExpressVariant extends Model
{

    protected $fillable = [
        'sku_id',
        'sku_attr',
        'ae_item_sku_info_d_t_o',
        'product_id',
        'ali_express_product_id',
        'sku_price',
        'offer_sale_price',
        'offer_bulk_sale_price',
        'min_shipping_fee_cent',
        'max_shipping_fee_cent',
        'min_delivery_day',
        'max_delivery_day',
        'delivery_option',
    ];

    protected $casts = [
        'delivery_option' => 'json',
    ];
}

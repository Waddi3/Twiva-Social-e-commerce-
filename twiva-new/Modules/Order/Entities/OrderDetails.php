<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\InfluencerTransactionsHistory;
use Modules\Influencer\Entities\InfluencerShopProductImages;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Sizes;
use Modules\User\Entities\User;


class OrderDetails extends Model
{
    use SoftDeletes;

    protected $table = 'order_details';
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'product_type',
        'quantity',
        'size',
        'color',
        'amount',
        'product_price',
        'product_retail_price',
        'product_discount_price',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
        'e_commerce',
    ];

    public function userInfo()
    {
        return $this->hasOne(User::class, 'id', 'influencer_id');
    }

    public function productDetails()
    {
        return $this->hasOne(InfluencerShopProducts::class, 'id', 'product_id')->withTrashed();
    }

    public function businessProductDetails()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function productImages()
    {
        return $this->hasMany(InfluencerShopProductImages::class, 'shop_product_id', 'product_id') ??
            $this->productDetails->actualDetail->productImages();
    }


    public function Order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->withTrashed();
    }

    public function earnings()
    {
        return $this->hasMany(InfluencerTransactionsHistory::class, 'request_id', 'id');
    }

    public function influencerInfo()
    {
        return $this->hasOne(User::class, 'id', 'influencer_id');
    }


    public function productFromInfluencerShop()
    {
        return $this->hasOne(InfluencerShopProducts::class, 'id', 'product_id')->withTrashed();
    }

    public function colorDetailes()
    {
        return $this->belongsTo(Colors::class, 'color', 'id');
    }

    public function sizeDetailes()
    {
        return $this->belongsTo(Sizes::class, 'size', 'id');
    }

    public function colorDetail()
    {
        return $this->belongsTo(Colors::class, 'color', 'id');
    }

    public function sizeDetail()
    {
        return $this->belongsTo(Sizes::class, 'size', 'id');
    }

    public function getFullAmountAttribute()
    {
        return $this->amount * $this->quantity;
    }

    public function getBusinessAmountAttribute()
    {
        if (is_null($this->product_price)) { // if all product_prices are null it is main the order is old otherwise is new
            $amount = $this->amount * $this->quantity;
            return $amount - (($amount * 9) / 100);
        } else {
            $amount = 0;
            $discount_amount = is_null($this->product_discount_price) ? 0 : ($this->product_retail_price - $this->product_discount_price);
            $price_net = $this->product_price - $discount_amount;

            if ($this->Order->shop->user_id == $this->Order->business_id) { // if owner shop is self business it is main all amount + 60% (price - retail_price) refer to business
                /*$earning = $this->product_retail_price - $this->product_price;
                $business_earning_60_percent = $earning * Order::Influencer60Percent;
                $price = $price_net + $business_earning_60_percent;*/
                $price = !is_null($this->product_discount_price) ? $this->product_price - ($this->product_retail_price - $this->product_discount_price) : $this->product_price;
            } else {
                $price = $price_net;
            }

            $amount += $this->quantity * $price;

            return $amount;
        }
    }

    public function getInfluencerAmountAttribute()
    {
        if (is_null($this->product_price)) { // if all product_prices are null it is main the order is old otherwise is new
            return $this->amount * Order::InfluencerPerCent;
        } else {
            $amount = 0;
            // check shop id
            // business get all price or discount_price
            // to check
            $amount_item = $this->amount;
            $discount_amount = is_null($this->product_discount_price) ? 0 : ($this->product_retail_price - $this->product_discount_price);
            $earning = $this->amount - $this->product_price + $discount_amount;
            $influencer_earning_60_percent = $earning * Order::Influencer60Percent;
            $amount += $this->quantity * $influencer_earning_60_percent;
            //            if ($this->id == 360) {
            //                dd(['amount_item' => $amount_item, 'business_earning' => $business_earning, 'price_60_percent' => $price_60_percent, 'amount' => $amount]);
            //            }
            return $amount;
        }

        //        return ($this->amount * $this->quantity) * .05;
    }

    public function productsHasShipping()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productsInfluencerHasShipping()
    {
        return $this->belongsTo(InfluencerShopProducts::class, 'product_id', 'id');
    }

    public function productsBusiness()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productsInfluencer()
    {
        return $this->belongsTo(InfluencerShopProducts::class, 'product_id', 'id');
    }

    public function getPriceAttribute()
    {
        if ($this->Order->shop->user->type == User::InfluencerType) {
            return (string)$this->productFromInfluencerShop?->product_price;
        }
        return is_null($this->product_discount_price) ? (string)$this->product_retail_price : (string)$this->product_discount_price;
    }

    public function getNameAttribute()
    {
        if ($this->Order->shop->user->type == User::InfluencerType) {
            return $this->productFromInfluencerShop?->product_title ?? 'undefined';
        }
        return $this->businessProductDetails->name ?? 'undefined';
    }

    public function getImageAttribute()
    {
        if ($this->Order->shop->user()->withTrashed()->first()->type == User::InfluencerType) {
            return $this->productFromInfluencerShop?->productMainImage?->imageUrl;
        }
        return $this->businessProductDetails->mainImagePath;
    }

    public function product()
    {
        if ($this->Order->shop->user->type == User::InfluencerType) {
            return $this->productsInfluencer();
        }
        return $this->productsBusiness();
    }
}

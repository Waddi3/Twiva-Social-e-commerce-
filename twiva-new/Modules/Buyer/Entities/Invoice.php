<?php

namespace Modules\Buyer\Entities;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class Invoice extends Model
{
    use SoftDeletes;

    const INVOICE_TYPE = 'invoice';
    const EXPRESS_TYPE = 'express';
    const PENDING_STATUS = 'pending';
    const PAID_STATUS = 'paid';
    const PAY_ON_DELIVERY_STATUS = 'pay_on_delivery';

    const FORMAT_DATA = 'Y-m-d';

    protected $fillable = [
        'slug',
        'user_id',
        'buyer_id',
        'status', // pending | paid | pay_on_delivery
        'type', // invoice | express
        'order_ids',
        'due_date',
        'message',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'order_ids' => 'collection'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function products()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id');
    }

    public function dueDate(): Attribute
    {
        return Attribute::get(fn($due_date) => Carbon::parse($due_date)->format('d M Y'));
    }

    public static function checkExistsSlug(string $slug)
    {
        return self::whereSlug($slug)->exists();
    }

    public static function generateSlug($length = 10)
    {
        static $try = 1;
        $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
        do
        {
            $length_start = $length_end = (int)floor($length / 3);
            $length_center = (int)ceil($length / 3);
//            $slug = Str::random($length_start) . '-' . Str::random($length_center) . '-' . Str::random($length_end);
            $slug = substr(str_shuffle(str_repeat($pool, $length_start)), 0, $length_start) . '-' . substr(str_shuffle(str_repeat($pool, $length_center)), 0, $length_center) . '-' . substr(str_shuffle(str_repeat($pool, $length_end)), 0, $length_end);
//            $slug = substr(str_shuffle(str_repeat($pool, $length)), 0, $length);;
        } while (self::checkExistsSlug($slug));
        return $slug;
    }

    public function buyerLink(): Attribute
    {
        return Attribute::get(function ($invoice, $attributes)
        {
            return route("buyer-{$attributes['type']}-link", ['slug' => $attributes['slug']]);
        });
    }

    public function scopeSlug($query, $slug)
    {
        return $query->whereSlug($slug);
    }

    public function scopeType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeStatus($query, $status = self::PENDING_STATUS)
    {
        return $query->whereStatus($status);
    }

    public function scopePending($query)
    {
        return $query->whereStatus(self::PENDING_STATUS);
    }

    public function scopeinvoice($query)
    {
        return $query->whereType(self::INVOICE_TYPE);
    }

    public function scopeExpress($query)
    {
        return $query->whereType(self::EXPRESS_TYPE);
    }

    public static function syncOrders($invoice, $order_ids, $payment_type)
    {
        if ($invoice)
        {
            $invoice = Invoice::find(session('invoice')['id']);
            if ($invoice)
            {
                $invoice->update([
                    'status' => Invoice::PAID_STATUS, // $payment_type == Transaction::PayOnDeliveryGateAway ? Invoice::PAY_ON_DELIVERY_STATUS : Invoice::PAID_STATUS,
                    'order_ids' => $order_ids,
                ]);
            }
        }
    }

    public static function prepareCart($invoice)
    {
        $cart = [];
        $shop_domain = $invoice->user->shop->domain;
        foreach ($invoice->products as $product)
        {
            if ($invoice->user->type == User::BusinessType)
            {
                $product_temp = Product::find($product->product_id);
            }
            else
            {
                $product_temp = InfluencerShopProducts::find($product->product_id);
            }
            $cart[$product->product_id] = [
                "id" => $product->product_id,
                "product_id" => $product->product_id,
                "quantity" => $product->quantity,
                "shop_domain" => $shop_domain,
                "color_id" => $product_temp->color_details->count() == 1 ? $product_temp->color_details->first()?->id : "",
                "size_id" => $product_temp->size_details->count() == 1 ? $product_temp->size_details->first()?->id : "",
            ];
        }
        return $cart;
    }
}

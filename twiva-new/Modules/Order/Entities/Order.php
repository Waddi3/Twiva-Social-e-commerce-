<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Buyer\Entities\Buyer;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Entities\CouponOrder;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\Product;
use Modules\Order\Events\CreatedOrderEvent;
use Modules\Order\Traits\CreateOrderTrait;
use Modules\Payment\Jobs\WithdrawForMasokoJob;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\User;


class Order extends Model {
    use SoftDeletes;
    use CreateOrderTrait;


    const Influencer60Percent = 0.6;
    protected $table = "orders";
    protected $dates = ['created_at', 'updated_at', 'delivery_date', 'deleted_at'];

    // fillable => payment_type,

    protected $guarded = [];
    // prices
    const InfluencerPerCent = .05;
    const ShippingPerCent = .07;
    const MINIMUM_COST_SHIPPING = 350;
    //order_status
    const WAITING_FOR_PAYMENT_STATUS = -2;
    const CANCELED_STATUS = -1;
    const PLACED_STATUS = 0;
    const PROCESSING_STATUS = 1;
    const DISPATCHED_STATUS = 2;
    const DELIVERED_STATUS = 3;

    const STATUSES = [
        self::WAITING_FOR_PAYMENT_STATUS => "Waiting for payment",
        self::CANCELED_STATUS            => "Canceled",
        self::PLACED_STATUS              => "Placed",
        self::PROCESSING_STATUS          => "Processing",
        self::DISPATCHED_STATUS          => "Dispatched",
        self::DELIVERED_STATUS           => "Delivered",
    ];


    // payment_status
    const NOT_PAID = 0;
    const PAID = 1;
    const PAY_ON_DELIVERY = 2;
    const CampaignProductIDFromInfluencerShop = 0;


    const PAYMENT_STATUS = [
        self::NOT_PAID        => 'NOT PAID',
        self::PAID            => 'PAID',
        self::PAY_ON_DELIVERY => 'PAY ON DELIVERY',
    ];

    const PAYMENT_STATUSES_CLASSES = [
        self::NOT_PAID        => 'eshop-failed',
        self::PAID            => 'eshop-active',
        self::PAY_ON_DELIVERY => 'eshop-upcoming',
        null                  => 'in-process'
    ];


    public function routeNotificationForOnfon($notification)
    {
        return $this->shippingAddress->mobile;
    }

    public function orderList()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function getQtyAttribute()
    {
        return $this->orderList->sum('quantity');
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id')->withTrashed();
    }

    public function buyerData()
    {
        return $this->hasOne(Buyer::class, 'id', 'buyer_id');
    }

    public function buyer()
    {
        return $this->buyerData();
    }

    // ToDo:: delete after push to live
    public function influencerInfo()
    {
        return $this->hasOne(User::class, 'id', 'influencer_id')->withTrashed();
    }

    // ToDo:: delete after push to live
    public function influencer()
    {
        return $this->influencerInfo();
    }

    public function business()
    {
        return $this->hasOne(User::class, 'id', 'business_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shops::class, 'shop_id', 'id');
    }

    public function deliveryIssues()
    {
        return $this->hasMany(DeliveryIssue::class);
    }

    public function getStatusStringAttribute()
    {
        return match ($this->order_status)
        {
            self::CANCELED_STATUS => "Canceled",
            self::PLACED_STATUS => "Placed",
            self::PROCESSING_STATUS => "Processing",
            self::DISPATCHED_STATUS => "Dispatched",
            self::DELIVERED_STATUS => "Delivered",
            self::WAITING_FOR_PAYMENT_STATUS => "Waiting for payment",
            default => "",
        };
    }

    public static function getStatusNumberFromString($type)
    {
        return match ($type)
        {
            "Canceled" => [self::CANCELED_STATUS],
            "Placed" => [self::PLACED_STATUS],
            "Processing" => [self::PROCESSING_STATUS, self::DISPATCHED_STATUS],
            "Dispatched" => [self::DISPATCHED_STATUS],
            "Delivered" => [self::DELIVERED_STATUS],
            "Waiting for payment" => [self::WAITING_FOR_PAYMENT_STATUS],
            default => [self::PLACED_STATUS, self::PROCESSING_STATUS, self::DISPATCHED_STATUS, self::DELIVERED_STATUS],
        };
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'address_id')->withTrashed();
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'orders_transactions')->where('request_type', Transaction::BUYER_PURCHASE);
    }

    public function latestTransaction()
    {
        return $this->transactions()->orderBy('id', 'DESC');
    }

    public function getBusinessAmountAttribute()
    {
        $order_details = $this->orderDetails;
        $old_calc = $order_details->every(function ($item)
        {
            return is_null($item->product_price);
        });
        if ($old_calc)
        { // if all product_prices are null it is main the order is old otherwise is new
            $amount = $this->order_amount;
            return $amount - (($amount * 9) / 100) + $this->shipping_amount;
        }
        else
        {
            $amount = 0;
            foreach ($order_details as $order_detail)
            {
                $amount += $order_detail->BusinessAmount;
            }
            return $amount; // + $this->shipping_amount;
        }
    }

    public function getInfluencerAmountAttribute()
    {
        $order_details = $this->orderDetails;
        $old_calc = $order_details->every(function ($item)
        {
            return is_null($item->product_price);
        });
        if ($old_calc)
        { // if all product_prices are null it is main the order is old otherwise is new
            return $this->order_amount * self::InfluencerPerCent;
        }
        else
        {
            $amount = 0;
            foreach ($order_details as $order_detail)
            {
                $amount += $order_detail->InfluencerAmount;
            }
            return $amount;
        }
    }

    public function getBuyerInfoAttribute()
    {
        if ($this->buyerData()->exists())
        {
            return (object)['id' => $this->buyerData->id, 'email' => $this->buyerData->email];
        }
        else
        {
            return (object)['id' => 999, 'email' => $this->shippingAddress?->email ?? 'guest@gmail.com'];
        }
    }

    public function setDeliveryCode()
    {
        if (!$this->delivery_code)
        {
            $this->delivery_code = self::generateDeliveryCode();
            $this->save();
        }
    }

    public static function generateDeliveryCode($length = 6, $max_try = 1000)
    {
        static $try = 1;
        $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
        //        $length_start = $length_end = (int)floor($length / 3);
        //        $length_center = (int)ceil($length / 3);
        //        $delivery_code = Str::random($length_start) . '-' . Str::random($length_center) . '-' . Str::random($length_end);
        //        $delivery_code = substr(str_shuffle(str_repeat($pool, $length_start)), 0, $length_start) . '-' . substr(str_shuffle(str_repeat($pool, $length_center)), 0, $length_center) . '-' . substr(str_shuffle(str_repeat($pool, $length_end)), 0, $length_end);
        $delivery_code = substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
        $exists = self::checkExistsDeliveryCode($delivery_code);
        if ($exists)
        {
            $try++;
            if ($try < $max_try)
            {
                self::generateDeliveryCode($length, $max_try);
            }
            return null;
        }
        return $delivery_code;
    }

    public static function checkExistsDeliveryCode(string $delivery_code)
    {
        return self::whereDeliveryCode($delivery_code)->exists();
    }

    public function tentative_period_text()
    {
        if (is_null($this->tentative_delivery_period))
        {
            return "";
        }
        $tentative_period = 'Your product will be shipped ';
        if ($this->tentative_delivery_period == 0)
        {
            $tentative_period .= 'today.';
        }
        elseif ($this->tentative_delivery_period == 1)
        {
            $tentative_period .= 'tomorrow.';
        }
        else
        {
            $tentative_period .= "within the next {$this->tentative_delivery_period} days.";
        }
        return $tentative_period;
    }

    public function getProductsDetails()
    {
        $products = collect();
        foreach ($this->orderDetails as $order_detail)
        {
            if ($order_detail->product instanceof Product)
            {
                $products->push([
                    'id'     => $order_detail?->product?->id,
                    'title'  => $order_detail?->product?->name,
                    'amount' => $order_detail?->amount,
                    'image'  => $order_detail?->product?->main_image_path,
                ]);
            }
            else
            {
                $products->push([
                    'id'     => $order_detail?->product?->id,
                    'title'  => $order_detail?->product?->product_title,
                    'amount' => $order_detail?->amount,
                    'image'  => $order_detail?->product?->actualDetail?->main_image_path,
                ]);
            }
        }
        return $products;
    }

    public function coupons()
    {
        return $this->hasManyThrough(Coupon::class, CouponOrder::class, 'order_ids', 'id', 'id', 'coupon_id')
            ->whereJsonContains('order_ids', $this->id);
    }

    public function getPaymentStatusTransactionClassAttribute()
    {
        if (is_null($this->latestTransaction->first()?->status) && $this->payment_status == self::PAID)
        {
            return Transaction::TRANSACTION_STATUSES_CLASSES[Transaction::PaymentSuccessfully]; // by coupon 100%
        }
        return ($this->payment_status != self::PAY_ON_DELIVERY) ? Transaction::TRANSACTION_STATUSES_CLASSES[$this->latestTransaction->first()?->status] : Transaction::TRANSACTION_STATUSES_CLASSES[null];
    }

    public function getPaymentStatusTransactionAttribute()
    {
        if (is_null($this->latestTransaction->first()?->status) && $this->payment_status == self::PAID)
        {
            return Transaction::TransactionStatuses[Transaction::PaymentSuccessfully]; // by coupon 100%
        }
        return ($this->payment_status != self::PAY_ON_DELIVERY) ? Transaction::TransactionStatuses[$this->latestTransaction->first()?->status] : Transaction::TransactionStatuses[null];
    }

    protected static function booted()
    {
        // Listen for the "created" event
        static::created(function ($order)
        {
            // Trigger an event when order created
        });

        // Listen for the "updated" event
        static::updated(function ($order)
        {
            // Trigger an event when order updated
            event(new CreatedOrderEvent($order));
            WithdrawForMasokoJob::dispatch($order);
        });
    }

    public function groupPaymentRequest(): BelongsTo
    {
        return $this->belongsTo(OrderGroupPaymentRequest::class, 'group_payment_request_id');
    }

    public function getGroupPaymentLinkAttribute(): ?string
    {
        return ($this->order_status == Order::WAITING_FOR_PAYMENT_STATUS && $this->groupPaymentRequest) ? route('group-pay', ['uuid' => $this->groupPaymentRequest->uuid]) : null;
    }
}

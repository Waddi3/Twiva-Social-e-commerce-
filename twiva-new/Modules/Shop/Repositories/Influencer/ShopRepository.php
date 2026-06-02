<?php


namespace Modules\Shop\Repositories\Influencer;


use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\One\User;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\BuyerOrderGroupPayment;
use Modules\Order\Entities\OrderGroupPaymentRequest;
use Modules\Shop\Entities\Shops;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlaced;
use Modules\Notification\Notifications\Buyer\OrderHasBeenPlacedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Events\DeliveredOrderEvent;
use NotificationChannels\Twilio\TwilioChannel;

class ShopRepository {
    private $account;

    public function __construct()
    {
        $this->account = request('account');
    }

    public function ShopData()
    {
        return $this->shopProductsQuery()->first();
    }

    public function getShopID()
    {
        return Shops::select('id', 'domain')->where('domain', $this->account)->first();
    }


    private function shopProductsQuery()
    {
        return Shops::select('id', 'user_id', 'shop_name', 'shop_cover_image_path', 'shop_logo_path', 'shop_description', 'facebook_domain_verification_code')
            ->with(
                [
                    'products'   => function ($query)
                    {
                        return $query
                            ->select('id', 'influencer_id', 'product_id', 'shop_id', 'product_title', 'product_price')
                            ->with([
                                'productMainImage' => function ($query)
                                {
                                    return $query;
                                    return $query->select(/*'shop_product_id',*/ 'image_path', 'is_cover_pic');
                                },
                                'actualDetail'     => function ($query)
                                {
                                    return $query->select('id', 'specification', 'color', 'size', 'is_ali_express as product_is_ali_express');
                                },
                            ])->where('status', 1)
                            ->whereHas('actualDetail');
                    },
                    'influencer' => function ($query)
                    {
                        return $query->select('id', 'name')->with(['UserPlatforms' => function ($query)
                        {
                            return $query->select('id', 'user_id', 'platform_id', 'social_id', 'additional_info', 'token', 'name')
                                ->whereNotNull('token');
                        }]);
                    },
                ])
            ->where(['is_active' => 1, 'deleted_at' => null, 'domain' => $this->account]);
    }

    private function getProductsQuery()
    {
        return InfluencerShopProducts::select('id', 'product_id', 'shop_id', 'product_title', 'product_price')
            ->with([
                'productMainImage' => function ($query)
                {
                    return $query->select('shop_product_id', 'image_path', 'is_cover_pic');
                },
                'actualDetail'     => function ($query)
                {
                    return $query->select('id', 'specification', 'color', 'size', 'brand', 'is_ali_express as product_is_ali_express');
                },
            ])
            ->whereHas('user')
            ->whereHas('actualDetail.user')
            ->whereHas('actualDetail')
            ->where(['shop_id' => $this->getShopID()->id]);
    }

    public function getSimilarProducts()
    {
        return $this->getProductsQuery()->inRandomOrder()->take(4)->get();
    }
    public function hasServicesProducts(): bool
    {
        return $this->getProductsQuery()
            ->whereHas('actualDetail.eventTicketProduct')
            ->exists();
    }

    // API
    public static function updateInfluencerShop($data)
    {
        $shop = Shops::where('user_id', auth('api')->id())->first();
        if (isset($data['shop_cover_image_path']))
        {
            $data['shop_cover_image_path'] = $data['shop_cover_image_path']->store(Shops::COVER_IMAGE_PATH, 'public');
            $data['shop_cover_image_path'] = str_replace(Shops::COVER_IMAGE_PATH . '/', '', $data['shop_cover_image_path']);
        }
        if (isset($data['shop_logo_path']))
        {
            $data['shop_logo_path'] = $data['shop_logo_path']->store(Shops::LOGO_PATH, 'public');
            $data['shop_logo_path'] = str_replace(Shops::LOGO_PATH . '/', '', $data['shop_logo_path']);
        }
        $influencers = array_key_exists('influencers', $data) ? $data['influencers'] : [];
        unset($data['influencers']);
        $shop->forInfluencers()->sync($influencers);
        $shop->update($data);
        return $shop;
    }

    public static function payForOrder($transaction)
    {
        $ordersIds = $transaction->orders->pluck('id')->toArray();
        $orders = Order::whereIn('id', $ordersIds)->get();
        self::NotifyPaidOrders($orders);
    }

    public static function groupPayForOrder(Transaction $transaction): void
    {

        $groupPayRequest = OrderGroupPaymentRequest::with(['buyerOrderGroupPayment'])
            ->where('id', $transaction->orders[0]->group_payment_request_id)
            ->first();

        BuyerOrderGroupPayment::create([
            'buyer_id'   => $transaction->user_id,
            'amount'     => $transaction->transaction_amount,
            'request_id' => $groupPayRequest->id,
            'paid_at'    => now()
        ]);

        if ($groupPayRequest->fresh()->all_paid_amount >= $groupPayRequest->amount)
        {
            self::NotifyPaidOrders($transaction->orders);
        }
    }

    public static function NotifyPaidOrders($orders)
    {
        $ordersIds = $orders->pluck('id')->toArray();
        foreach ($orders as $order)
        {
            // TODO:: 1. remove additional queries 2. convert this code to function to use it every where need to pay
            $order->update(['payment_status' => Order::PAID, 'order_status' => Order::PLACED_STATUS]);
            $order->orderList()->update(['status' => Order::PAID]);
            event(new DeliveredOrderEvent($order));
            if ($order->shop->user->type == \Modules\User\Entities\User::InfluencerType)
            {
                foreach ($order->orderDetails as $product)
                {
                    $product->productFromInfluencerShop->update(['total_sale' => $product->productFromInfluencerShop->total_sale + $product->quantity]);
                    $product->productDetails->actualDetail->update(['total_sale' => $product->productDetails->actualDetail->total_sale + $product->quantity]);
                }
            }
            else if ($order->shop->user->type == \Modules\User\Entities\User::BusinessType)
            {
                foreach ($order->orderDetails as $product)
                {
                    $product->businessProductDetails->update(['total_sale' => $product->businessProductDetails->total_sale + $product->quantity]);
                }
            }
            $order->business->notify(new NewOrderReceivedNotification($order));
        }
        if ($orders[0]->shop->user->type == \Modules\User\Entities\User::InfluencerType)
        {
            $orders[0]->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
        }
        $notificationEmail = $orders[0]?->buyer->email ?? $orders[0]->shippingAddress->email;
        $notificationPhoneNumber = $orders[0]?->buyer->mobile ?? $orders[0]->shippingAddress->phone_number;

        // Notification::route('mail', $notificationEmail)
        // ->route(TwilioChannel::class, '+' . str_replace('-', '', $notificationPhoneNumber))
        $orders[0]->shippingAddress->notify(new OrderHasBeenPlacedNotification($ordersIds));
    }

    public static function payCouponFreeForOrder($orders)
    {
        // $orders = Order::whereIn('id', $ordersIds)->get();

        foreach ($orders as $order)
        {
            $order->business->notify(new NewOrderReceivedNotification($order));
            $order->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
            if ($order->shop->user->type == \Modules\User\Entities\User::InfluencerType)
            {
                foreach ($order->orderDetails as $product)
                {
                    $product->productFromInfluencerShop->update(['total_sale' => $product->productFromInfluencerShop->total_sale + $product->quantity]);
                    $product->productDetails->actualDetail->update(['total_sale' => $product->productDetails->actualDetail->total_sale + $product->quantity]);
                }
            }
            else
            {
                foreach ($order->orderDetails as $order_details)
                {
                    $order_details->businessProductDetails->update(['total_sale' => $order_details->businessProductDetails->total_sale + $order_details->quantity]);
                }
            }
        }
        Session::put('payment_type', 'freeCoupon');
        self::NotifyPaidOrders($orders);
        session()->remove('coupon_with_shipping_fees');
    }

}

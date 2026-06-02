<?php

namespace Modules\Buyer\Repositories\Order;

use Illuminate\Support\Facades\Session;
use Modules\AliExpress\Repositories\AliExpressRepository;
use Modules\Buyer\Entities\Buyer;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Entities\CouponBuyer;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Modules\Payment\Services\MPESAService;
use Modules\Shop\Services\ShopProductService;
use Modules\User\Entities\User;

class ApiBuyerOrderRepository
{
    protected $buyer;
    protected $user;
    public $product_ids;
    public $products;
    public $validation_product_selection_size_color;

    public function __construct()
    {
        $this->buyer = auth('buyer-api')->user();
        $this->user = User::with('shop')->find($this->buyer->created_by);
        // dd($this->user);
    }

    public function getProductIds(mixed $data)
    {
        $cart = $data['cart'];
        $this->product_ids = array_keys($cart);
        return $this->product_ids;
    }

    public function getProducts()
    {
        $this->products = Product::products($this->user, $this->product_ids);
        return $this->products;
    }
    public function checkIfProductsBelongToAliExpressAndReturnNames(): string
    {
        $aliExpressRepository = new AliExpressRepository();
        \Log::debug('ApiBuyerOrderController=>create', [
            'twiva.ali-express.business-account' => config('twiva.ali-express.business-account'),
            '$product_ids'                       => $this->product_ids,
            'checkIfProductsBelongToAliExpressAndReturnNames'        => $aliExpressRepository->checkIfProductsBelongToAliExpressAndReturnNames($this->user->type, $this->product_ids)
        ]);
        return $aliExpressRepository->checkIfProductsBelongToAliExpressAndReturnNames($this->user->type, $this->product_ids);
    }

    public function validationColorSize($data)
    {
        $cart = $data['cart'];
        $account = $this->user->shop->domain;
        $this->validation_product_selection_size_color = ShopProductService::checkShoppingProductSizeColorSelections($account, $cart);
        return $this->validation_product_selection_size_color;
    }

    public function create(mixed $data)
    {
        // check the color and size of each product
        // save order(s) with details
        // check shipping fees
        // return orders id
        $orders = $this->storeOrder($data);
        $this->storeTransaction($orders, $data);
        $order_ids = $orders->pluck('id')->toArray();
        $order_amount = $orders->sum('order_amount');
        $shipping_amount = $orders->sum('shipping_amount');
        $coupon_discount = $orders->sum('coupon_discount');
        return ['orders' => $orders, 'order_ids' => $order_ids, 'order_amount' => $order_amount, 'shipping_amount' => $shipping_amount, 'coupon_discount' => $coupon_discount];
    }

    public function storeOrder($data)
    {
        $buyer = auth('buyer-api')->user();
        $coupon = Coupon::where('code', $data['coupon_code'])->first();
        $coupon_buyer = null;
        if ($coupon) {
            $coupon_buyer = CouponBuyer::firstOrCreate(
                ['coupon_id' => $coupon->id, 'buyer_id' => $buyer->id],
                ['amount' => $coupon->amount, 'type' => $coupon->type]
            );
        }
        $cart = collect($data['cart']);
        $first_porduct = $this->products->first();
        $group_by = 'actualDetail.user_id';
        if ($first_porduct instanceof Product) { // buy from business products
            $group_by = 'user_id';
        }
        $products = $this->products->groupBy($group_by);
        $address = ShippingAddress::where('id', $data['address_id'])->first();

        $newOrders = [];
        foreach ($products as $business_id => $shop_products) {
            $order_amount = 0;
            $order = Order::create([
                'buyer_id' => auth('buyer-api')->id() ?? null,
                'shop_id' => $this->user->shop->id,
                'business_id' => $business_id,
                'address_id' => $data['address_id'],
                'partner_id' => $data['partner_id'] ?? null,
                'order_status' => Order::WAITING_FOR_PAYMENT_STATUS,
                'order_amount' => $order_amount,
                'payment_status' => Order::NOT_PAID,
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
            ]);
            $newOrders[] = $order;

            if ($first_porduct instanceof Product) { // business
                foreach ($shop_products as $product) {
                    $productPrice = $product->discount ?? $product->retail_price;
                    $orderDetails = OrderDetails::create(
                        [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'amount' => $productPrice,
                            'product_price' => $product->price,
                            'product_retail_price' => $product->retail_price,
                            'product_discount_price' => $product->discount,
                            'quantity' => $cart->firstWhere('product_id', $product->id)['quantity'] ?? 1,
                            'size' => $cart->firstWhere('product_id', $product->id)['size_id'] ?? null,
                            'color' => $cart->firstWhere('product_id', $product->id)['color_id'] ?? null,
                        ]
                    );
                    $order_amount += $orderDetails->quantity * $productPrice;
                }
            } else { // influencer
                foreach ($shop_products as $product) {
                    $orderDetails = OrderDetails::create(
                        [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'amount' => $product->product_price,
                            'product_price' => $product->productDetails->price,
                            'product_retail_price' => $product->productDetails->retail_price,
                            'product_discount_price' => $product->productDetails->discount,
                            'quantity' => $cart->firstWhere('product_id', $product->id)['quantity'] ?? 1,
                            'size' => $cart->firstWhere('product_id', $product->id)['size_id'] ?? null,
                            'color' => $cart->firstWhere('product_id', $product->id)['color_id'] ?? null,
                        ]
                    );
                    $order_amount += $orderDetails->quantity * $product->product_price;
                }
            }

            $partnerId = $data['partner_id'] ?? null;
            $shipping_fees = ShippingService::calculateShippingFee($this->products, $address?->city_id, $partnerId);
            // $shippingAmount = $shipping_fees;
            $shippingAmount = ($shipping_fees / count($products));

            $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $order_amount, $shippingAmount, count($products));
            $order->update(['order_amount' => round($order_amount), 'shipping_amount' => $shippingAmount, 'coupon_discount' => $coupon_discount]);
        }
        $newOrders = collect($newOrders);

        // notifications send
        return $newOrders;
    }

    public function storeTransaction($orders, $data)
    {
        $ordersAmount = 0;
        foreach ($orders as $order) {
            $ordersAmount += ($order->shipping_amount + $order->order_amount);
        }
        $buyer = auth('buyer-api')->user();
        $transaction_details['transaction_amount'] = round($ordersAmount);
        $transaction_details['user_id'] = $buyer->id ?? null;
        $transaction_details['request_type'] = Transaction::BUYER_PURCHASE;
        $transaction_details['request_id'] = json_encode($orders->pluck('id')->toArray());
        $transaction_details['phone_number'] = $buyer->phone_code . Buyer::removeSpace($buyer->phone_number);
        $transaction_details['status'] = Transaction::WaitingForPayment;


        if ($data['payment_type'] == 'MPESA') {
            $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
        }
        if ($data['payment_type'] == 'PayOnDelivery') {
            foreach ($orders as $order) {
                $order->update(['order_status' => Order::PLACED_STATUS, 'payment_status' => Order::PAY_ON_DELIVERY]);
                $order->business->notify(new NewOrderReceivedNotification($order));
                $order->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
                if ($order->shop->user->type == User::InfluencerType) {
                    foreach ($order->orderDetails as $product) {
                        $product->productFromInfluencerShop->update(['total_sale' => $product->productFromInfluencerShop->total_sale + $product->quantity]);
                        $product->productDetails->actualDetail->update(['total_sale' => $product->productDetails->actualDetail->total_sale + $product->quantity]);
                    }
                } else {
                    foreach ($order->orderDetails as $order_details) {
                        $order_details->businessProductDetails->update(['total_sale' => $order_details->businessProductDetails->total_sale + $order_details->quantity]);
                    }
                }
            }
        }

        $transaction_details['transaction_details'] = json_encode($transaction_details);
        $transaction = Transaction::create($transaction_details);

        foreach ($orders as $order) {
            $order->transactions()->attach($transaction->id);
        }
    }

    public function checkTransactionStatus($transaction)
    {
        $response = MPESAService::checkTransactionStatus($transaction);
        $response_body = json_decode($response->body());
        logs()->info('api buyer order repository checkTransactionStatus => ', ['response' => $response, 'transaction' => $transaction, 'response_body' => $response_body]);
        $transaction->update([
            'originator_conversation_id' => $response_body->OriginatorConversationID ?? null,
            'conversation_id' => $response_body->conversation_id ?? null,
        ]);
    }

    public function calculateOrderAmount(mixed $data)
    {
        $approximationAmountOrder = $this->approximationAmountOrder($data);
        $order_amount = $approximationAmountOrder->sum('order_amount');
        $shipping_amount = $approximationAmountOrder->sum('shipping_amount');
        return ['order_amount' => $order_amount, 'shipping_amount' => $shipping_amount];
    }

    public function approximationAmountOrder($data)
    {
        $cart = collect($data['cart']);
        $first_porduct = $this->products->first();
        $group_by = 'actualDetail.user_id';
        if ($first_porduct instanceof Product) { // buy from business products
            $group_by = 'user_id';
        }
        $products = $this->products->groupBy($group_by);
        $address = ShippingAddress::where('id', $data['address_id'])->first();

        $approximationAmountOrder = [];
        foreach ($products as $business_id => $shop_products) {
            $order_amount = 0;

            if ($first_porduct instanceof Product) { // business
                foreach ($shop_products as $product) {
                    $productPrice = $product->discount ?? $product->retail_price;
                    $quantity = $cart->firstWhere('product_id', $product->id)['quantity'] ?? 1;
                    $order_amount += $quantity * $productPrice;
                }
            } else { // influencer
                foreach ($shop_products as $product) {
                    $quantity = $cart->firstWhere('product_id', $product->id)['quantity'] ?? 1;
                    $order_amount += $quantity * $product->product_price;
                }
            }

            $partnerId = $data['partner_id'] ?? null;
            $shipping_fees = ShippingService::calculateShippingFee($this->products, $address?->city_id, $partnerId);
            // $shippingAmount = $shipping_fees;
            $shippingAmount = ($shipping_fees / count($products));

            $approximationAmountOrder[] = [
                'order_amount' => round($order_amount),
                'shipping_amount' => $shippingAmount
            ];
        }
        return collect($approximationAmountOrder);
    }
}

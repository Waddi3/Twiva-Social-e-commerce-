<?php


namespace Modules\Shop\Repositories\Business;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\ShippingAddress;
use Modules\Payment\Services\FlutterWaveService;
use Modules\Payment\Services\MPESAService;

class BuyerRepository
{
    private $shopID;
    private ShopRepository $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
        $this->shopID = $shopRepository->getShop()->id ?? null;
    }

    public function storeOrder($account)
    {
        $productFromSession = $this->getProductFromSession();
        $productsByBusinesses = $this->getProductFromSession()['productsData']->groupBy('actualDetail.user_id');
        $newOrders = [];
        foreach ($productsByBusinesses as $business_id => $influencerShopProducts) {
            $order_amount = 0;
            $shippingAmount = 0;
            $countOfHasShippingFeesProducts = 0;
            $order = Order::create([
                'buyer_id' => Auth::guard('buyer')->id() ?? null,
                'shop_id' => $this->shopID,
                'business_id' => $business_id,
                'address_id' => Session::get('address_id'),
                'order_status' => Order::WAITING_FOR_PAYMENT_STATUS,
                'order_amount' => $order_amount,
                'payment_status' => Order::NOT_PAID,
                'delivery_instructions' => session()->get('delivery_instructions'),
            ]);
            $newOrders[] = $order;
            foreach ($influencerShopProducts as $product) {
                $orderDetails = OrderDetails::create(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'business_id' => $business_id,
                        'amount' => $product->product_price,
                        'product_price' => $product->productDetails->price,
                        'product_retail_price' => $product->productDetails->retail_price,
                        'product_discount_price' => $product->productDetails->discount,
                        'quantity' => $productFromSession['buyerProducts'][$product->id]['quantity'],
                        'size' => $productFromSession['buyerProducts'][$product->id]['size_id'] ?? null,
                        'color' => $productFromSession['buyerProducts'][$product->id]['color_id'] ?? null,
                    ]);
                if ($product->productDetails->has_shipping_fees) {
                    $shippingAmount += ($orderDetails->quantity * $product->product_price) * Order::ShippingPerCent;
                    $countOfHasShippingFeesProducts++;
                }
                $order_amount += $orderDetails->quantity * $product->product_price;
            }

            if ($countOfHasShippingFeesProducts) {
                $shippingAmount = round(max($shippingAmount, Order::MINIMUM_COST_SHIPPING));
                $shippingAmount = 350;
            } else {
                $shippingAmount = round(max($shippingAmount, 0));
            }
            $shippingAmount = $shippingAmount == Order::MINIMUM_COST_SHIPPING ? ($shippingAmount / count($productsByBusinesses)) : $shippingAmount;
            $shippingAmount = round(max(Order::MINIMUM_COST_SHIPPING, $order_amount * Order::ShippingPerCent));

            $order->update(['order_amount' => $order_amount, 'shipping_amount' => $shippingAmount]);
        }
        $newOrders = collect($newOrders);
        Session::put('orders_ids', $newOrders->pluck('id')->toArray());
        return $newOrders;
    }

    public function getProductFromSession()
    {
        $data = [];
        $cartsInfo = Session::get('cart') ?? [];
        $data['buyerProducts'] = $cartsInfo;
        $data['productsData'] = InfluencerShopProducts::whereIn('id', array_keys($cartsInfo))
            ->select('id', 'product_title', 'product_price', 'product_id', 'user_id')
            ->with([
                'productDetails:id,user_id,price,retail_price,discount',
                'productMainImage' => function ($productMainImage) {
                    return $productMainImage->select('id', 'shop_product_id', 'image_path');
                }, 'actualDetail' => function ($actualDetail) {
                    return $actualDetail->select('id', 'color', 'size', 'user_id');
                }])->get();
        $data['total'] = 0;
        $data['quantity'] = 0;
        foreach ($data['productsData'] as $product) {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $data['total'] += $product->quantity * $product->product_price;
            $data['quantity'] += $product->quantity;
        }
        return $data;
    }

    public function storeTransaction($orders, $data, $account)
    {
        $ordersAmount = 0;
        foreach ($orders as $order) {
            $ordersAmount += ($order->shipping_amount + $order->order_amount);
        }
        $transaction_details['transaction_amount'] = round($ordersAmount);
        $transaction_details['user_id'] = Auth::guard('buyer')->id() ?? null;
        $transaction_details['request_type'] = Transaction::BUYER_PURCHASE;
        $transaction_details['request_id'] = json_encode($orders->pluck('id')->toArray());
        $transaction_details['phone_number'] = $data['phone_number'];
        $transaction_details['status'] = Transaction::WaitingForPayment;
        $gatewayResponse = '';
        if ($data['payment_type'] == Transaction::MPESAGateAway) {
            list($gatewayResponse, $transaction_details) = $this->payWithMpesa($transaction_details
            );
        }
        if ($data['payment_type'] == Transaction::PayOnDeliveryGateAway) {
            $this->payOnDeliver($orders);
        }
        $transaction_details['transaction_details'] = json_encode($transaction_details);
        $transaction = Transaction::create($transaction_details);

        if ($data['payment_type'] == Transaction::FlutterWaveGateAway) {
            return $this->payWithFlutterwave($transaction_details, $account, $orders);
        }
        foreach ($orders as $order) {
            $order->transactions()->attach($transaction->id);
        }
        return [$transaction, $gatewayResponse];

    }

    private function payWithMpesa(array $transaction_details): array
    {
        $gatewayResponse = MPESAService::createTransaction($transaction_details['transaction_amount'], $transaction_details['phone_number']);
        if (isset(json_decode($gatewayResponse)->errorMessage)) {
            $transaction_details['status'] = Transaction::TransactionFailed;
        }
        $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
        $transaction_details['transaction_id'] = json_decode($gatewayResponse)->MerchantRequestID ?? null;
        Session::put('transaction_id', json_decode($gatewayResponse)->MerchantRequestID ?? null);
        return array($gatewayResponse, $transaction_details);
    }

    private function payWithFlutterwave(array $transaction_details, $account, $orders): array
    {
        list($MerchantRequestID, $redirectLink) = FlutterWaveService::createTransactionForShopSite($transaction_details);
        $transaction_details['payment_gateway_type'] = Transaction::FlutterWaveGateAway;
        $transaction_details['transaction_id'] = $MerchantRequestID;
        $transaction_details['phone_number'] = ShippingAddress::find(Session::get('address_id'))->phone_number;
        Session::put('transaction_id', $MerchantRequestID);
        Session::put('flutterwave_payment_url', route('buyer.checkout.confirm', ['account' => $account]));
        $transaction_details['flutterwave_payment_url'] = route('buyer.checkout.confirm', ['account' => $account]);
        $transaction_details['transaction_details'] = json_encode($transaction_details);
        unset($transaction_details['flutterwave_payment_url']);
        $transaction = Transaction::create($transaction_details);
        foreach ($orders as $order) {
            $order->transactions()->attach($transaction->id);
        }
        return [$MerchantRequestID, $redirectLink];
    }

    private function payOnDeliver($orders): void
    {
        foreach ($orders as $order) {
            $order->update(['order_status' => Order::PLACED_STATUS, 'payment_status' => Order::PAY_ON_DELIVERY]);
            $order->business->notify(new NewOrderReceivedNotification($order));
            $order->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
            foreach ($order->orderDetails as $product) {


                $product->productFromInfluencerShop->update(['total_sale' => $product->productFromInfluencerShop->total_sale + $product->quantity]);
                $product->productDetails->actualDetail->update(['total_sale' => $product->productDetails->actualDetail->total_sale + $product->quantity]);
            }
        }
        Session::put('payment_type', 'PayOnDeliveryGateAway');

    }
}

<?php


namespace Modules\Shop\Repositories\Influencer;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Entities\CouponBuyer;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Modules\Payment\Services\FlutterWaveService;
use Modules\Payment\Services\MPESAService;
use Modules\Payment\Services\PesapalService;
use Modules\Payment\Services\SpotitService;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\User;

class BuyerRepository
{
    private $shopID;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopID = $shopRepository->getShopID()->id ?? null;
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public function storeOrderForInfluencerShops($account)
    {
        $productFromSession = $this->getProductFromSession($account);
        $products_grouped_by_business = $this->getProductFromSession($account)['productsData']->groupBy('actualDetail.user_id');

        // $count_of_all_orders = 0;

        // foreach ($products_grouped_by_business as $business_id => $influencer_products) {
        //     $productsByBusinesses = $influencer_products->groupBy('shop_id');
        //     $count_of_all_orders += count($productsByBusinesses);
        // }

        $newOrders = Order::createOrders($productFromSession, $products_grouped_by_business, 0);

        // // Calculate shipping fee
        // $allProducts = new Collection();
        // foreach ($productsByBusinesses as $business_id => $influencerShopProducts) {
        //     $allProducts = $allProducts->merge($influencerShopProducts);
        // }
        // $addressId = Session::get('address_id');
        // $address = ShippingAddress::find($addressId);
        // $partnerId = Session::get('partner_id');
        // $shippingFees = ShippingService::calculateShippingFee($allProducts, $address?->city_id, $partnerId);
        // $shippingFees = $shippingFees / count($productsByBusinesses);

        // foreach ($productsByBusinesses as $business_id => $influencerShopProducts) {
        //     $order_amount = 0;
        //     //$countOfHasShippingFeesProducts = 0;
        //     $order = Order::create([
        //         'buyer_id' => Auth::guard('buyer')->id() ?? null,
        //         'shop_id' => $this->shopID,
        //         'business_id' => $business_id,
        //         'address_id' => Session::get('address_id'),
        //         'partner_id' => Session::get('partner_id'),
        //         'order_status' => Order::WAITING_FOR_PAYMENT_STATUS,
        //         'order_amount' => $order_amount,
        //         'payment_status' => Order::NOT_PAID,
        //         'delivery_instructions' => session()->get('delivery_instructions'),
        //     ]);
        //     $newOrders[] = $order;

        //     foreach ($influencerShopProducts as $product) {
        //         $orderDetails = OrderDetails::create(
        //             [
        //                 'order_id' => $order->id,
        //                 'product_id' => $product->id,
        //                 'amount' => $product->product_price,
        //                 'product_price' => $product->productDetails->price,
        //                 'product_retail_price' => $product->productDetails->retail_price,
        //                 'product_discount_price' => $product->productDetails->discount,
        //                 'quantity' => $productFromSession['buyerProducts'][$product->id]['quantity'],
        //                 'size' => $productFromSession['buyerProducts'][$product->id]['size_id'] ?? null,
        //                 'color' => $productFromSession['buyerProducts'][$product->id]['color_id'] ?? null,
        //             ]
        //         );
        //         $order_amount += $orderDetails->quantity * $product->product_price;
        //     }


        //     $coupon_buyer = session('coupon_buyer');
        //     $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $order_amount, $shippingFees, count($productsByBusinesses));
        //     $order->update(['order_amount' => round($order_amount), 'shipping_amount' => $shippingFees, 'coupon_discount' => $coupon_discount]);
        // }
        $newOrders = collect($newOrders);
        Session::put('orders_ids', $newOrders->pluck('id')->toArray());
        return $newOrders;
    }

    public function storeOrderForBusinessShops($account)
    {
        $productFromSession = $this->getProductFromSession($account);
        $products_grouped_by_business = $this->getProductFromSession($account)['productsData']->groupBy('user_id');

        $newOrders = Order::createOrders($productFromSession, $products_grouped_by_business, 0);

        // // // dd($productsByBusinesses);
        // // $newOrders = [];
        // // // Calculate shipping fee
        // // $allProducts = new Collection();
        // // foreach ($productsByBusinesses as $business_id => $businessShopProducts) {
        // //     $allProducts = $allProducts->merge($businessShopProducts);
        // // }
        // // $addressId = Session::get('address_id');
        // // $address = ShippingAddress::find($addressId);
        // // $partnerId = Session::get('partner_id');
        // // $shippingFees = ShippingService::calculateShippingFee($allProducts, $address?->city_id, $partnerId, true);
        // // $shippingFees = $shippingFees / count($productsByBusinesses);

        // // foreach ($productsByBusinesses as $business_id => $businessShopProducts) {
        // //     $order_amount = 0;
        // //     $order = Order::create([
        // //         'buyer_id' => Auth::guard('buyer')->id() ?? null,
        // //         'shop_id' => $this->shopID,
        // //         'business_id' => $business_id,
        // //         'address_id' => Session::get('address_id'),
        // //         'partner_id' => Session::get('partner_id'),
        // //         'order_status' => Order::WAITING_FOR_PAYMENT_STATUS,
        // //         'order_amount' => $order_amount,
        // //         'payment_status' => Order::NOT_PAID,
        // //         'delivery_instructions' => session()->get('delivery_instructions'),
        // //     ]);
        // //     $newOrders[] = $order;

        // //     foreach ($businessShopProducts as $product) {
        // //         $productPrice = $product->discount ?? $product->retail_price;
        // //         $orderDetails = OrderDetails::create(
        // //             [
        // //                 'order_id' => $order->id,
        // //                 'product_id' => $product->id,
        // //                 'amount' => $productPrice,
        // //                 'product_price' => $product->price,
        // //                 'product_retail_price' => $product->retail_price,
        // //                 'product_discount_price' => $product->discount,
        // //                 'quantity' => $productFromSession['buyerProducts'][$product->id]['quantity'],
        // //                 'size' => $productFromSession['buyerProducts'][$product->id]['size_id'] ?? null,
        // //                 'color' => $productFromSession['buyerProducts'][$product->id]['color_id'] ?? null,
        // //             ]
        // //         );
        // //         $order_amount += $orderDetails->quantity * $productPrice;
        // //     }


        //     $coupon_buyer = session('coupon_buyer');
        //     $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $order_amount, $shippingFees, count($productsByBusinesses));
        //     $order->update(['order_amount' => round($order_amount), 'shipping_amount' => $shippingFees, 'coupon_discount' => $coupon_discount]);
        // }
        $newOrders = collect($newOrders);
        Session::put('orders_ids', $newOrders->pluck('id')->toArray());
        return $newOrders;
    }

    public function storeOrder($account)
    {
        $coupon_code = session('coupon_code');
        $coupon_key = session('coupon_key');
        $buyer = auth('buyer')->user();
        if ($coupon_key) {
            $caches = cache($coupon_key);
            if ($caches && ($buyer?->id == $caches['buyer']?->id || session()->getId() == $caches['session_id'])) {
                $coupon = array_key_exists('coupon', $caches) ? $caches['coupon'] : null;
                if ($coupon) {
                    $coupon_buyer = CouponBuyer::firstOrCreate(
                        ['coupon_id' => $coupon->id, 'buyer_id' => $buyer?->id],
                        ['amount' => $coupon->amount, 'type' => $coupon->type]
                    );
                    session(['coupon_buyer' => $coupon_buyer]);
                }
            }
        }
        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false) {
            return $this->storeOrderForInfluencerShops($account);
        } else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false) {
            return $this->storeOrderForBusinessShops($account);
        }
        abort(404);
    }

    public function getProductFromSession($account)
    {

        if ($this->shopData($account)?->user?->type == User::InfluencerType ?? false) {
            return $this->getInfluencerProductsFromSession();
        } else if ($this->shopData($account)?->user?->type == User::BusinessType ?? false) {
            return $this->getBusinessProductsFromSession();
        }
        abort(404);
    }

    private function getInfluencerProductsFromSession()
    {
        $data = [];
        $cartsInfo = Session::get('cart') ?? [];
        $data['buyerProducts'] = $cartsInfo;
        $data['productsData'] = InfluencerShopProducts::whereIn('id', array_keys($cartsInfo))
            ->select('id', 'product_title', 'product_price', 'product_id', 'influencer_id', 'shop_id')
            ->with([
                'productDetails:id,user_id,price,retail_price,discount,has_shipping_fees',
                'productMainImage' => function ($productMainImage) {
                    return $productMainImage->select('id', 'shop_product_id', 'image_path');
                }, 'actualDetail' => function ($actualDetail) {
                    return $actualDetail->select('id', 'color', 'size', 'user_id', 'has_shipping_fees', 'total_sale', 'price', 'retail_price', 'discount');
                }
            ])->get();
        $data['total'] = 0;
        $data['quantity'] = 0;
        $data['type'] = 'Influencer';

        foreach ($data['productsData'] as $product) {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $data['total'] += $product->quantity * $product->product_price;
            $data['quantity'] += $product->quantity;
            $data['has_shipping_fees'] = !is_null($product->productDetails?->business) && !is_null($product->productDetails?->business->businessShipping->has_shipping_fees) ? $product->productDetails?->business->businessShipping->has_shipping_fees : 0;
        }
        return $data;
    }

    private function getBusinessProductsFromSession()
    {
        $data = [];
        $cartsInfo = Session::get('cart') ?? [];
        $data['buyerProducts'] = $cartsInfo;
        $data['productsData'] = Product::whereIn('products.id', array_keys($cartsInfo))
            ->select('products.id', 'name', 'price', 'retail_price', 'discount', 'products.user_id', 'size', 'color', 'has_shipping_fees', 'shops.id as shop_id')
            ->leftJoin('shops', 'shops.user_id', '=', 'products.user_id')
            ->with([
                'productImages' => function ($productMainImage) {
                    return $productMainImage->select('product_image.id', 'product_image.is_cover_pic', 'product_image.image');
                }
            ])->get();
        $data['total'] = 0;
        $data['quantity'] = 0;
        $data['type'] = 'Business';

        foreach ($data['productsData'] as $product) {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $price = $product->discount ?? $product->retail_price;
            $data['total'] += $product->quantity * $price;
            $data['quantity'] += $product->quantity;
            $data['has_shipping_fees'] = !is_null($product->productDetails?->business) && !is_null($product->productDetails?->business?->businessShipping?->has_shipping_fees)  ? $product->productDetails?->business->businessShipping->has_shipping_fees : 0;
        }
        return $data;
    }

    public function storeTransaction($orders, $data, $account)
    {
        $ordersAmount = 0;
        foreach ($orders as $order) {
            $ordersAmount += (($order->shipping_amount + $order->order_amount) - $order->coupon_discount);
        }
        $transaction_details['transaction_amount'] = round($ordersAmount);
        $transaction_details['user_id'] = Auth::guard('buyer')->id() ?? null;
        $transaction_details['request_type'] = Transaction::BUYER_PURCHASE;
        $transaction_details['request_id'] = json_encode($orders->pluck('id')->toArray());
        $transaction_details['phone_number'] = $data['payment_type'] != 6 ? $data['phone_number'] : $data['spotit_phone_number'];
        $transaction_details['status'] = Transaction::WaitingForPayment;
        $gatewayResponse = '';
        if ($data['payment_type'] == Transaction::MPESAGateAway) {
            list($gatewayResponse, $transaction_details) = $this->payWithMpesa($transaction_details);
        }
        if ($data['payment_type'] == Transaction::PayOnDeliveryGateAway) {
            $this->payOnDeliver($orders);
        }
        if ($data['payment_type'] == Transaction::SpotitGateAway) {
            $transaction_details = (new SpotitService())->payWithSpotit($transaction_details, $orders, $data);
        }
        if ($data['payment_type'] == Transaction::PesapalGateAway) {
            list($transaction_details, $gatewayResponse) = (new PesapalService())->pay($transaction_details, $orders, $data, route('buyer.checkout.confirm', ['account' => $account]));
        }
        $transaction_details['transaction_details'] = json_encode($transaction_details);
        if(isset($transaction_details['request_response'])){
            unset($transaction_details['request_response']);
        }
        if(isset($transaction_details['confirm_url'])){
            unset($transaction_details['confirm_url']);
        }
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
        Session::put('payment_type', 'PayOnDeliveryGateAway');
    }

    public function hasOrderPayOnDelivery($products) {
        foreach($products as $product) {
            if( $product instanceof InfluencerShopProducts ) {
                $payOnDelivery = $product->productDetails->business->businessShipping->pay_on_delivery;
            }
            // Product
            else {
                $payOnDelivery = $product->business->businessShipping->pay_on_delivery;
            }

            if( !$payOnDelivery ) {
                return $payOnDelivery;
            }
        }
        return 1;
    }
}

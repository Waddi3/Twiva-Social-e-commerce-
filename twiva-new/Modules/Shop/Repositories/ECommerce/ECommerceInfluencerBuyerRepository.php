<?php


namespace Modules\Shop\Repositories\ECommerce;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Coupon\Entities\CouponBuyer;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Notification\Notifications\Business\NewOrderReceivedNotification;
use Modules\Notification\Notifications\Influencer\PurchaseHasBeenMadeFromEshopNotification;
use Modules\Order\Entities\BuyerOrderGroupPayment;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;
use Modules\Order\Entities\OrderGroupPaymentRequest;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Modules\Payment\Services\FlutterWaveService;
use Modules\Payment\Services\MPESAService;
use Modules\Payment\Services\PesapalService;
use Modules\Payment\Services\SpotitService;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Repositories\Influencer\ShopRepository;
use Modules\User\Entities\User;
use Str;

class ECommerceInfluencerBuyerRepository {
    private $shopID;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopID = $shopRepository->getShopID()->id ?? null;
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public function storeOrderForInfluencerShops()
    {

        $productFromSession = $this->getProductFromSession()['influencer_products'];
        $products_grouped_by_business = $this->getProductFromSession()['influencer_products']['productsData']->groupBy('actualDetail.user_id');

        $newOrders = Order::createOrders($productFromSession, $products_grouped_by_business, 1);


        $newOrders = collect($newOrders);
        if (Session::has('orders_ids'))
        {
            $existingOrders = Session::get('orders_ids');
            $updatedOrders = array_merge($existingOrders, $newOrders->pluck('id')->toArray());
            Session::put('orders_ids', $updatedOrders);
        }
        else
        {
            Session::put('orders_ids', $newOrders->pluck('id')->toArray());
        }
        return [$newOrders, 0];
    }

    public function storeOrderForBusinessShops()
    {
        return [collect(), 0];
        $productFromSession = $this->getProductFromSession()['business_products'];
        $productsByBusinesses = $this->getProductFromSession()['business_products']['productsData']->groupBy('user_id');
        $newOrders = [];
        $businesses_shippingFees = 0;

        // Calculate shipping fee
        $allProducts = new Collection();
        foreach ($productsByBusinesses as $business_id => $businessShopProducts)
        {
            $allProducts = $allProducts->merge($businessShopProducts);
        }

        if (!count($productsByBusinesses))
        {
            return [collect(), 0];
        }

        foreach ($productsByBusinesses as $business_id => $businessShopProducts)
        {
            $shop_id = $this->getShopIdFromUserId($business_id);
            $order_amount = 0;


            $addressId = Session::get('address_id');
            $address = ShippingAddress::find($addressId);
            $partnerId = Session::get('partner_id');
            $shippingFees = ShippingService::calculateShippingFee($businessShopProducts, $address?->city_id, $partnerId, true);


            $order = Order::create([
                'buyer_id'              => Auth::guard('buyer')->id() ?? null,
                'shop_id'               => $shop_id,
                'business_id'           => $business_id,
                'partner_id'            => Session::has('partner_id') ? Session::get('partner_id') : null,
                'address_id'            => Session::get('address_id'),
                'order_status'          => Order::WAITING_FOR_PAYMENT_STATUS,
                'order_amount'          => $order_amount,
                'payment_status'        => Order::NOT_PAID,
                'delivery_instructions' => session()->get('delivery_instructions'),
                'e_commerce'            => 1
            ]);
            $newOrders[] = $order;

            foreach ($businessShopProducts as $product)
            {
                $productPrice = $product->discount ?? $product->retail_price;
                $orderDetails = OrderDetails::create(
                    [
                        'order_id'               => $order->id,
                        'product_id'             => $product->id,
                        'product_type'           => $product->getMorphClass(),
                        'amount'                 => $productPrice,
                        'product_price'          => $product->price,
                        'product_retail_price'   => $product->retail_price,
                        'product_discount_price' => $product->discount,
                        'quantity'               => $productFromSession['buyerProducts'][$product->id]['quantity'],
                        'size'                   => $productFromSession['buyerProducts'][$product->id]['size_id'] ?? null,
                        'color'                  => $productFromSession['buyerProducts'][$product->id]['color_id'] ?? null,
                        'e_commerce'             => 1
                    ]
                );
                $order_amount += $orderDetails->quantity * $productPrice;
            }

            $coupon_buyer = session('coupon_buyer');
            $coupon_discount = CouponBuyer::relateCouponWithOrderAndGetDiscount($coupon_buyer, $order, $order_amount, $shippingFees, count($productsByBusinesses));
            $order->update(['order_amount' => round($order_amount), 'shipping_amount' => $shippingFees, 'coupon_discount' => $coupon_discount]);
        }
        $newOrders = collect($newOrders);
        if (Session::has('orders_ids'))
        {
            $existingOrders = Session::get('orders_ids');
            $updatedOrders = array_merge($existingOrders, $newOrders->pluck('id')->toArray());
            Session::put('orders_ids', $updatedOrders);
        }
        else
        {
            Session::put('orders_ids', $newOrders->pluck('id')->toArray());
        }
        return [$newOrders, $businesses_shippingFees];
    }

    public function storeOrder()
    {
        $coupon_code = session('coupon_code');
        $coupon_key = session('coupon_key');
        $buyer = auth('buyer')->user();
        if ($coupon_key)
        {
            $caches = cache($coupon_key);
            if ($caches && ($buyer?->id == $caches['buyer']?->id || session()->getId() == $$caches['session_id']))
            {
                $coupon = array_key_exists('coupon', $caches) ? $caches['coupon'] : null;
                if ($coupon)
                {
                    $coupon_buyer = CouponBuyer::firstOrCreate(
                        ['coupon_id' => $coupon->id, 'buyer_id' => $buyer?->id],
                        ['amount' => $coupon->amount, 'type' => $coupon->type]
                    );
                    session(['coupon_buyer' => $coupon_buyer]);
                }
            }
        }
        [$influencer_orders, $influencers_shipping_fee] = $this->storeOrderForInfluencerShops();
        [$business_orders, $businesses_shipping_fee] = $this->storeOrderForBusinessShops();
        $orders = $influencer_orders->merge($business_orders);
        return $orders;
        abort(404);
    }

    public function getProductFromSession()
    {
        $influencer_products = $this->getInfluencerProductsFromSession();
        $business_products = $this->getBusinessProductsFromSession();
        $all_products = $this->mergeProductsSession($influencer_products, $business_products);
        $products = ['influencer_products' => $influencer_products, 'business_products' => $business_products, 'all_products' => $all_products];
        return $products;
    }

    public function mergeProductsSession($influencer_products, $business_products)
    {
        $all_products = [
            'buyerProducts'     => $influencer_products['buyerProducts'],
            'productsData'      => $influencer_products['productsData']->merge($business_products['productsData']),
            'total'             => $influencer_products['total'] + $business_products['total'],
            'quantity'          => $influencer_products['quantity'] + $business_products['quantity'],
            'type'              => [$influencer_products['type'], $business_products['type']],
            'has_shipping_fees' => max($influencer_products['has_shipping_fees'] ?? null, $business_products['has_shipping_fees'] ?? null),
        ];
        return $all_products;
    }

    private function getInfluencerProductsFromSession()
    {
        $data = [];
        $cartsInfo = Session::get('cart') ?? [];
        $data['buyerProducts'] = $cartsInfo;
        $products_ids = collect($cartsInfo)->where('user_type', User::InfluencerType)->pluck('product_id');
        $data['productsData'] = InfluencerShopProducts::whereIn('id', $products_ids)
            ->select('id', 'product_title', 'product_price', 'product_id', 'influencer_id', 'shop_id')
            ->whereHas('user')
            ->whereHas('actualDetail')
            ->whereHas('actualDetail.user')
            ->active()
            ->whereHas('actualDetail', function ($whereHas)
            {
                $whereHas->active()->where('stock', '>', 0);
            })
            ->with([
                'productDetails:id,user_id,price,retail_price,discount,has_shipping_fees',
                'productMainImage' => function ($productMainImage)
                {
                    return $productMainImage->select('id', 'shop_product_id', 'image_path');
                },
                'actualDetail'     => function ($actualDetail)
                {
                    return $actualDetail->select('id', 'color', 'size', 'user_id', 'has_shipping_fees', 'total_sale', 'price', 'retail_price', 'discount');
                }
            ])->get()->append(['user_type']);
        $data['total'] = 0;
        $data['quantity'] = 0;
        $data['type'] = 'Influencer';

        foreach ($data['productsData'] as $product)
        {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $data['total'] += $product->quantity * $product->product_price;
            $data['quantity'] += $product->quantity;
            if (is_null($product->actualDetail))
            {
                logger('check null has shipping fees => ', ['product' => $product->id, 'buyer0' => auth()->id(), 'buyer' => auth('buyer')->id(), 'cartsInfo' => $cartsInfo]);
            }
            $data['has_shipping_fees'] = $product->actualDetail->has_shipping_fees;
        }
        return $data;
    }

    private function getBusinessProductsFromSession()
    {
        $data = [];
        $cartsInfo = Session::get('cart') ?? [];
        $data['buyerProducts'] = $cartsInfo;
        $products_ids = collect($cartsInfo)->where('user_type', User::BusinessType)->pluck('product_id');
        $data['productsData'] = Product::whereIn('id', $products_ids)
            ->select('id', 'name', 'price', 'retail_price', 'discount', 'user_id', 'size', 'color', 'has_shipping_fees')
            ->whereHas('user')
            ->active()
            ->where('stock', '>', 0)
            ->with([
                'productImages' => function ($productMainImage)
                {
                    return $productMainImage->select('id', 'is_cover_pic', 'image');
                }
            ])->get()->append(['user_type']);
        $data['total'] = 0;
        $data['quantity'] = 0;
        $data['type'] = 'Business';

        foreach ($data['productsData'] as $product)
        {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $price = $product->discount ?? $product->retail_price;
            $data['total'] += $product->quantity * $price;
            $data['quantity'] += $product->quantity;
            $data['has_shipping_fees'] = $product->has_shipping_fees;
        }
        return $data;
    }

    public function storeTransaction($orders, $data)
    {

        $transaction_details['request_id'] = json_encode($orders->pluck('id')->toArray());
        $transaction_details['user_id'] = Auth::guard('buyer')->id() ?? null;
        $transaction_details['phone_number'] = Transaction::getPhoneNumber($data);
        $transaction_details['status'] = Transaction::WaitingForPayment;
        $gatewayResponse = '';

        $groupPaymentRequest = Order::where('id', $orders[0]->id)->first()->groupPaymentRequest;
        if ($groupPaymentRequest)
        {
            $transaction_details['request_type'] = Transaction::BUYER_PURCHASE_GROUP_PAY;
            $transaction_details['transaction_amount'] = BuyerOrderGroupPayment::where([
                'buyer_id'   => \auth('buyer')->id(),
                'request_id' => $groupPaymentRequest->id,
                'paid_at'    => null
            ])->latest()->first()->amount;
        }
        else
        {
            $ordersAmount = 0;
            foreach ($orders as $order)
            {
                $ordersAmount += (($order->shipping_amount + $order->order_amount) - $order->coupon_discount);
            }
            $transaction_details['request_type'] = Transaction::BUYER_PURCHASE;
            $transaction_details['transaction_amount'] = round($ordersAmount);
        }

        if ($data['payment_type'] == Transaction::MPESAGateAway)
        {
            list($gatewayResponse, $transaction_details) = $this->payWithMpesa($transaction_details);
        }
        if ($data['payment_type'] == Transaction::PayOnDeliveryGateAway)
        {
            $this->payOnDeliver($orders);
        }
        if ($data['payment_type'] == Transaction::SpotitGateAway)
        {
            $transaction_details = (new SpotitService())->payWithSpotit($transaction_details, $orders, $data);
        }
        if ($data['payment_type'] == Transaction::PesapalGateAway) {
            list($transaction_details, $gatewayResponse) = (new PesapalService())->pay($transaction_details, $orders, $data, route('e-commerce.buyer.checkout.confirm'));
        }
        $transaction_details['transaction_details'] = json_encode($transaction_details);
        if (isset($transaction_details['request_response']))
        {
            unset($transaction_details['request_response']);
        }
        if(isset($transaction_details['confirm_url'])){
            unset($transaction_details['confirm_url']);
        }
        $transaction = Transaction::create($transaction_details);

        if ($data['payment_type'] == Transaction::FlutterWaveGateAway)
        {
            return $this->payWithFlutterwave($transaction_details, $orders);
        }
        foreach ($orders as $order)
        {
            $order->transactions()->attach($transaction->id);
        }
        return [$transaction, $gatewayResponse];
    }

    private function payWithMpesa(array $transaction_details): array
    {
        $gatewayResponse = MPESAService::createTransaction($transaction_details['transaction_amount'], $transaction_details['phone_number']);
        if (isset(json_decode($gatewayResponse)->errorMessage))
        {
            $transaction_details['status'] = Transaction::TransactionFailed;
        }
        $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
        $transaction_details['transaction_id'] = json_decode($gatewayResponse)->MerchantRequestID ?? null;
        Session::put('transaction_id', json_decode($gatewayResponse)->MerchantRequestID ?? null);
        return array($gatewayResponse, $transaction_details);
    }

    private function payWithFlutterwave(array $transaction_details, $orders): array
    {
        list($MerchantRequestID, $redirectLink) = FlutterWaveService::createTransactionForShopSite($transaction_details);
        $transaction_details['payment_gateway_type'] = Transaction::FlutterWaveGateAway;
        $transaction_details['transaction_id'] = $MerchantRequestID;
        $transaction_details['phone_number'] = ShippingAddress::find(Session::get('address_id'))->phone_number;
        Session::put('transaction_id', $MerchantRequestID);
        Session::put('flutterwave_payment_url', route('e-commerce.buyer.checkout.confirm'));
        $transaction_details['flutterwave_payment_url'] = route('e-commerce.buyer.checkout.confirm');
        $transaction_details['transaction_details'] = json_encode($transaction_details);
        unset($transaction_details['flutterwave_payment_url']);
        $transaction = Transaction::create($transaction_details);
        foreach ($orders as $order)
        {
            $order->transactions()->attach($transaction->id);
        }
        return [$MerchantRequestID, $redirectLink];
    }

    private function payOnDeliver($orders): void
    {
        foreach ($orders as $order)
        {
            $order->update(['order_status' => Order::PLACED_STATUS, 'payment_status' => Order::PAY_ON_DELIVERY]);
            $order->business->notify(new NewOrderReceivedNotification($order));
            $order->shop->influencer->notify(new PurchaseHasBeenMadeFromEshopNotification());
            if ($order->shop->user->type == User::InfluencerType)
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
        Session::put('payment_type', 'PayOnDeliveryGateAway');
    }

    public function getShopIdFromUserId($user_id)
    {
        return Shops::select('id', 'user_id')->where('user_id', $user_id)->first()?->id;
    }

    public function hasShippingFees($products)
    {
        foreach ($products as $product)
        {
            // Influencer Shop Products
            if (get_class($product) == InfluencerShopProducts::class)
            {
                $shippingByPartner = !is_null($product->productDetails->business?->businessShipping) ? $product->productDetails->business->businessShipping->shipping_by_partner : 1;
            }
            // Product
            else
            {
                $shippingByPartner = !is_null($product->business?->businessShipping) ? $product->business->businessShipping->shipping_by_partner : 0;
            }

            if ($shippingByPartner)
            {
                return $shippingByPartner;
            }
        }
        return false;
    }

    public function hasOrderPayOnDelivery($products)
    {
        foreach ($products as $product)
        {
            if ($product instanceof InfluencerShopProducts)
            {
                $payOnDelivery = $product->productDetails->business->businessShipping->pay_on_delivery;
            }
            // Product
            else
            {
                $payOnDelivery = $product->business->businessShipping->pay_on_delivery;
            }

            if (!$payOnDelivery)
            {
                return $payOnDelivery;
            }
        }
        return 1;
    }

    public function createGroupPayRequest($orders, $cart, $buyerAmount, $buyerId)
    {
        $ordersAmount = 0;
        foreach ($orders as $order)
        {
            $ordersAmount += (($order->shipping_amount + $order->order_amount) - $order->coupon_discount);
        }
        $uuid = Str::uuid()->toString();
        $groupPaymentRequestId = OrderGroupPaymentRequest::create([
            'amount' => $ordersAmount,
            'cart'   => $cart,
            'uuid'   => $uuid,
        ])->id;
        BuyerOrderGroupPayment::create([
            'request_id' => $groupPaymentRequestId,
            'amount'     => $buyerAmount,
            'buyer_id'   => $buyerId,
        ]);
        foreach ($orders as $order)
        {
            Order::where('id', $order->id)->update(['group_payment_request_id' => $groupPaymentRequestId]);
        }

    }
}

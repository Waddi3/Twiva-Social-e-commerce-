<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Buyer\Entities\Invoice;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderGroupPaymentRequest;
use Modules\Order\Entities\ShippingAddress;
use Modules\Shop\Http\Requests\ShopSite\CheckoutStoreAddressRequest;
use Modules\Shop\Http\Requests\ShopSite\PayRequest;
use Modules\Shop\Repositories\ECommerce\ECommerceInfluencerBuyerRepository;
use Modules\Shop\Repositories\ECommerce\OrderGroupPayRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository;
use Modules\Shop\Services\ECommerceShopProductService;
use Modules\Shop\Services\ShopProductService;
use Modules\User\Entities\User;

class ECommerceCheckoutController extends Controller {
    private ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository;

    public function __construct(ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository, private OrderGroupPayRepository $orderGroupPayRepository)
    {
        $this->e_commerce_buyer_repository = $e_commerce_buyer_repository;
    }

    // public function expressAccountPreview($account,$data)
    // {
    //     $attach_products = json_decode(\Crypt::decrypt($data),true);
    //     $cart = [];
    //     foreach ($attach_products as $attach_product)
    //     {
    //         $cart[$attach_product['product_id']] = $attach_product;
    //     }
    //     Session::put('cart', $cart);
    //     Session::save();
    //     return view('shop::checkout.account', [
    //         'productsData' => $this->e_commerce_buyer_repository->getProductFromSession($account),
    //         'is_preview' => true,
    //     ]);
    // }


    public function accountPage()
    {
        $count = 0;
        foreach ($this->e_commerce_buyer_repository->getProductFromSession() as $product)
        {
            $count += count($product['productsData']);
        }
        //dd($count);
        if (!$count)
        {
            return redirect()->route('ecommerce.index')->with(['errorMessage' => 'Your Basket is empty']);
        }
        if (Auth::guard('buyer')->check())
        {
            return redirect()->route('e-commerce.buyer.checkout.shipping');
        }
        $partners = User::where('type', User::PartnerType)->get();
        $productsData = $this->e_commerce_buyer_repository->getProductFromSession();
        //dd($this->e_commerce_buyer_repository->getProductFromSession());
        $hasShippingFees = $this->e_commerce_buyer_repository->hasShippingFees($productsData['all_products']['productsData']);
        Session::remove('partner_id');
        if ($hasShippingFees && $partners->count())
        {
            Session::put('partner_id', $partners[0]->id);
        }
        return view('front::checkout.account', [
            'partners'        => $partners,
            'productsData'    => $productsData,
            'hasShippingFees' => $hasShippingFees
        ]);
    }

    public function shippingPage()
    {
        $cart = Session::get('cart');
        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id()) {
            auth('buyer')->user()->update([
                'cart' => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        Session::remove('address_id');
        Session::remove('city_id');

        $count = 0;
        foreach ($this->e_commerce_buyer_repository->getProductFromSession() as $product)
        {
            $count += count($product['productsData']);
        }
        if (!$count)
        {
            return redirect()->route('ecommerce.index')->with(['errorMessage' => 'Your Basket is empty']);
        }
        $buyer = Auth::guard('buyer')->user();
        $partners = User::where('type', User::PartnerType)->get();
        $productsData = $this->e_commerce_buyer_repository->getProductFromSession();
        $hasShippingFees = $this->e_commerce_buyer_repository->hasShippingFees($productsData['all_products']['productsData']);
        return view(
            'front::checkout.shipping',
            [
                'partners'        => $partners,
                'buyer'           => $buyer,
                'addresses'       => $buyer->addresses ?? [],
                'countries'       => Country::select('id', 'name')->get(),
                'productsData'    => $productsData,
                'hasShippingFees' => $hasShippingFees
            ]
        );
    }

    public function storeShippingInfo(CheckoutStoreAddressRequest $request)
    {
        Session::remove('transaction_id');
        $address_id = null;
        $data = $request->validated();
        $delivery_instructions = $data['delivery_instructions'] ?? null;
        if ($data['address_type'] == 'exists')
        {
            $address_id = $data['address_id'];
        }
        else if ($data['address_type'] == 'new')
        {
            $data['buyer_id'] = Auth::guard('buyer')->id() ?? null;
            unset($data['address_type'], $data['address_id']);
            $data['phone_number'] = str_replace('+', '', $data['phone_number']);
            unset($data['delivery_instructions']);
            $address = ShippingAddress::create($data);
            $address_id = $address->id;
        }
        else
        {
            abort(403);
        }

        $validation_product_selection_size_color = ECommerceShopProductService::checkShoppingProductSizeColorSelections();
        if (!$validation_product_selection_size_color)
        {
            return redirect()
                ->back()
                ->withInput($data)
                ->with(['errorMessage' => 'Please select the size and color of the product(s).']);
        }

        Session::put('address_id', $address_id);
        session()->put('delivery_instructions', $delivery_instructions);
        session()->save();
        //        dd(session()->get('delivery_instructions'), session()->all());
        // dd(checkIfCouponFullyCoversOrder());
        if (checkIfCouponFullyCoversOrder())
        {
            $orders = $this->e_commerce_buyer_repository->storeOrder();
            removeCouponSessionCache();
            ShopRepository::payCouponFreeForOrder($orders);
            return redirect()->route('e-commerce.buyer.checkout.confirm');
        }
        return redirect()->route('e-commerce.buyer.checkout.payment');
    }

    public function paymentPage()
    {
        $count = 0;
        foreach ($this->e_commerce_buyer_repository->getProductFromSession() as $product)
        {
            $count += count($product['productsData']);
        }
        Session::remove('transaction_id');
        if (!$count)
        {
            return redirect()->route('ecommerce.index')->with(['errorMessage' => 'Your Basket is empty']);
        }
        $partners = User::where('type', User::PartnerType)->get();
        $productsData = $this->e_commerce_buyer_repository->getProductFromSession();
        $hasShippingFees = $this->e_commerce_buyer_repository->hasShippingFees($productsData['all_products']['productsData']);
        if (Session::has('address_id'))
        {
            return view('front::checkout.payment', [
                'productsData'    => $this->e_commerce_buyer_repository->getProductFromSession(),
                'partners'        => $partners,
                'hasShippingFees' => $hasShippingFees,
                'payOnDelivery'   => $this->e_commerce_buyer_repository->hasOrderPayOnDelivery($productsData['all_products']['productsData'])
            ]);
        }
        return redirect()->route('e-commerce.buyer.checkout.shipping');
    }

    public function pay(PayRequest $request)
    {
        Session::remove('transaction_id');
        Session::remove('payment_type');
        $data = $request->validated();
        if (!Session::has('address_id'))
        {
            return redirect()
                ->route('e-commerce.buyer.checkout.shipping')
                ->with(['errorMessage' => 'You need to select an address to possessed']);
        }

        if (!Session::has('cart'))
        {
            return redirect()->route('ecommerce.index')->with(['errorMessage' => 'Your Basket is empty']);
        }

        $validation_product_selection_size_color = ECommerceShopProductService::checkShoppingProductSizeColorSelections();
        if (!$validation_product_selection_size_color)
        {
            return redirect()
                ->back()
                ->withInput($data)
                ->with(['errorMessage' => 'Please select the size and color of the product(s).']);
        }

        $orders = $this->e_commerce_buyer_repository->storeOrder();
        if ($data['amount_payment_group_buying'] && ($data['is_group_paying'] ?? false))
        {
            $this->e_commerce_buyer_repository->createGroupPayRequest(
                orders: $orders,
                cart: Session::get('cart'),
                buyerAmount: $data['amount_payment_group_buying'],
                buyerId: \auth('buyer')->id()
            );
        }
        removeCouponSessionCache();
        if (checkIfCouponFullyCoversOrder())
        {
            ShopRepository::payCouponFreeForOrder($orders);
        }
        else
        {
            if ($data['payment_type'] == Transaction::MPESAGateAway)
            {
                list($transaction, $response) = $this->e_commerce_buyer_repository->storeTransaction($orders, $data);
                if (isset(json_decode($response)->errorMessage))
                {
                    Session::put('transaction_id', $transaction->transaction_id ?? null);
                    return redirect()->route('e-commerce.buyer.checkout.payment')
                        ->with(['errorMessage' => json_decode($response)->errorMessage]);
                }
            }
            if ($data['payment_type'] == Transaction::FlutterWaveGateAway)
            {
                list($MerchantRequestID, $redirectLink) = $this->e_commerce_buyer_repository->storeTransaction($orders, $data);
                return Redirect::to($redirectLink);
            }
            if ($data['payment_type'] == Transaction::PayOnDeliveryGateAway)
            {
                $this->e_commerce_buyer_repository->storeTransaction($orders, $data);
            }
            if ($data['payment_type'] == Transaction::SpotitGateAway)
            {
                $this->e_commerce_buyer_repository->storeTransaction($orders, $data);
            }
            if ($data['payment_type'] == Transaction::PesapalGateAway) {
                list($MerchantRequestID, $redirectLink) = $this->e_commerce_buyer_repository->storeTransaction($orders, $data);
                return redirect()->to($redirectLink);
            }
        }
        return redirect()->route('e-commerce.buyer.checkout.confirm');
    }

    public function confirmPage()
    {
        $orders = Order::whereIn('id', Session::get('orders_ids', []))
            ->with(['groupPaymentRequest' => function ($groupPaymentRequestQuery)
            {
                $groupPaymentRequestQuery->select(['id', 'uuid']);
            }])->get();
        if ((!Session::has('orders_ids') || !Session::has('transaction_id')) && !(Session::get('payment_type') == 'PayOnDeliveryGateAway' || Session::get('payment_type') == 'freeCoupon'))
        {
            return redirect()->route('e-commerce.buyer.checkout.payment');
        }
        $orders = Order::whereIn('id', Session::get('orders_ids', []))
            ->with(['groupPaymentRequest' => function ($groupPaymentRequestQuery)
            {
                $groupPaymentRequestQuery->select(['id', 'uuid', 'amount']);
            }])->get();
        if (!$orders->count())
        {
            return redirect()->route('ecommerce.index')->with(['errorMessage' => 'Your Basket is empty']);
        }
        $partners = User::where('type', User::PartnerType)->get();
        if (!$partners->isEmpty() && !Session::get('partner_id'))
        {
            Session::put('partner_id', $partners[0]->id);
        }
        if ($orders[0]->groupPaymentRequest)
        {
            $groupPaymentRequestData = $this->orderGroupPayRepository->getRequestData($orders[0]->groupPaymentRequest);
        }
        return view('front::checkout.confirm', [
            'productsData'            => $this->e_commerce_buyer_repository->getProductFromSession(),
            'orders'                  => $orders,
            'partners'                => $partners,
            'groupPayLink'            => $orders[0]->group_payment_link,
            'groupPaymentRequestData' => $groupPaymentRequestData ?? null
        ]);
    }

    public function checkOrderStatus()
    {
        if (Session::get('payment_type') == 'PayOnDeliveryGateAway' || Session::get('payment_type') == 'freeCoupon')
        {
            Session::remove('orders_ids');
            Session::remove('productsData');
            Session::remove('cart');
            Session::remove('coupon_with_shipping_fees');
            return response()->json(
                [
                    'transaction_status' => 1,
                ]
            );
        }
        $transaction = Transaction::where('transaction_id', Session::get('transaction_id'))->first();
        if (!Session::has('transaction_id') || !$transaction || $transaction->status == Transaction::TransactionFailed)
        {
            return response()->json(
                [
                    'transaction_status' => Transaction::TransactionFailed,
                ]
            );
        }
        if ($transaction->status == Transaction::PaymentSuccessfully)
        {
            if (auth('buyer')->check()) {
                auth('buyer')->user()->update([
                    'cart' => null,
                    'cart_last_updated_date' => null,
                ]);
            }
            Session::remove('transaction_id');
            Session::remove('orders_ids');
            Session::remove('productsData');
            Session::remove('cart');
            Session::remove('flutterwave_payment_url');
            Session::remove('address_id');
            Session::remove('city_id');
            return response()->json(
                [
                    'transaction_status' => Transaction::PaymentSuccessfully,
                ]
            );
        }
        return response()->json(
            [
                'transaction_status' => Transaction::WaitingForPayment,
            ]
        );
    }
}

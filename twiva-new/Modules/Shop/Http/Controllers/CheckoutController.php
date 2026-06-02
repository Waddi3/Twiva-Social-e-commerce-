<?php

namespace Modules\Shop\Http\Controllers;

use Crypt;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\Invoice;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\ShippingAddress;

use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Requests\ShopSite\CheckoutStoreAddressRequest;
use Modules\Shop\Http\Requests\ShopSite\PayRequest;
use Modules\Shop\Repositories\Influencer\BuyerRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository;
use Modules\Shop\Services\ShopProductService;
use Modules\User\Entities\User;

class CheckoutController extends Controller
{
    private BuyerRepository $buyerRepository;

    public function __construct(BuyerRepository $buyerRepository)
    {
        $this->buyerRepository = $buyerRepository;
    }

    public function expressAccountPreview($account, $data)
    {
        $attach_products = json_decode(Crypt::decrypt($data), true);
        $cart = [];
        foreach ($attach_products as $attach_product) {
            $cart[$attach_product['product_id']] = $attach_product;
        }
        Session::put('cart', $cart);
        Session::save();

        $partners = User::where('type', User::PartnerType)
            ->get();
        return view('shop::checkout.account', [
            'productsData' => $this->buyerRepository->getProductFromSession($account),
            'is_preview' => true,
            'partners' => $partners
        ]);
    }


    public function accountPage($account)
    {
        Session::remove('address_id');
        Session::remove('city_id');

        $partners = User::where('type', User::PartnerType)
            ->get();
        if (request()->exists('cart')) {
            session(['cart' => request('cart'), 'invoice' => request('invoice')]);
            if (request()->exists('invoice')) {
                if (request('invoice')['type'] == Invoice::EXPRESS_TYPE) {
                    Auth::guard('buyer')->loginUsingId(request('invoice')['buyer_id']);
                }
            }
        }
        if (!count($this->buyerRepository->getProductFromSession($account)['productsData'])) {
            return redirect()->route('shop.product.index', ['account' => $account])->with(['errorMessage' => 'Your Basket is empty']);
        }
        if (Auth::guard('buyer')->check()) {
            return redirect()->route('buyer.checkout.shipping', ['account' => $account]);
        }
        return view('shop::checkout.account', [
            'productsData' => $this->buyerRepository->getProductFromSession($account),
            'partners' => $partners
        ]);
    }

    public function shippingPage($account)
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

        //Session::remove('partner_id');
        if (!count($this->buyerRepository->getProductFromSession($account)['productsData'])) {
            return redirect()->route('shop.product.index', ['account' => $account])->with(['errorMessage' => 'Your Basket is empty']);
        }
        $buyer = Auth::guard('buyer')->user();

        $partners = User::where('type', User::PartnerType)->get();
        if (!$partners->isEmpty() && !Session::get('partner_id')) {
            Session::put('partner_id', $partners[0]->id);
        }

        return view(
            'shop::checkout.shipping',
            [
                'buyer' => $buyer,
                'addresses' => $buyer->addresses ?? [],
                'countries' => Country::select('id', 'name')->get(),
                'productsData' => $this->buyerRepository->getProductFromSession($account),
                'partners' => $partners
            ]
        );
    }

    public function storeShippingInfo($account, CheckoutStoreAddressRequest $request)
    {
        Session::remove('transaction_id');
        $address_id = null;
        $data = $request->validated();
        $delivery_instructions = $data['delivery_instructions'] ?? null;
        if ($data['address_type'] == 'exists') {
            $address_id = $data['address_id'];
        } else if ($data['address_type'] == 'new') {
            $data['buyer_id'] = Auth::guard('buyer')->id() ?? null;
            unset($data['address_type'], $data['address_id']);
            $data['phone_number'] = str_replace('+', '', $data['phone_number']);
            unset($data['delivery_instructions']);
            $address = ShippingAddress::create($data);
            $address_id = $address->id;
        } else {
            abort(403);
        }

        $validation_product_selection_size_color = ShopProductService::checkShoppingProductSizeColorSelections($account);
        if (!$validation_product_selection_size_color) {
            return redirect()
                ->back()
                ->withInput($data)
                ->with(['errorMessage' => 'Please select the size and color of the product(s).']);
        }

        Session::put('address_id', $address_id);
        session()->put('delivery_instructions', $delivery_instructions);
        session()->save();
        if (checkIfCouponFullyCoversOrder()) {
            $orders = $this->buyerRepository->storeOrder($account);
            removeCouponSessionCache();
            ShopRepository::payCouponFreeForOrder($orders);
            return redirect()->route('buyer.checkout.confirm', ['account' => $account]);
        }
        //        dd(session()->get('delivery_instructions'), session()->all());
        return redirect()->route('buyer.checkout.payment', ['account' => request('account')]);
    }

    public function paymentPage($account)
    {
        $partners = User::where('type', User::PartnerType)
            ->get();

        Session::remove('transaction_id');
        $products = $this->buyerRepository->getProductFromSession($account);
        if (!count($products['productsData'])) {
            return redirect()->route('shop.product.index', ['account' => $account])->with(['errorMessage' => 'Your Basket is empty']);
        }
        if (Session::has('address_id')) {
            return view('shop::checkout.payment', [
                'productsData' => $products,
                'partners' => $partners,
                'payOnDelivery' => $this->buyerRepository->hasOrderPayOnDelivery($products['productsData'])
            ]);
        }
        return redirect()->route('buyer.checkout.shipping', ['account' => request('account')]);
    }

    public function pay($account, PayRequest $request)
    {
        Session::remove('transaction_id');
        Session::remove('payment_type');
        $data = $request->validated();
        if (!Session::has('address_id')) {
            return redirect()
                ->route('buyer.checkout.shipping', ['account' => request('account')])
                ->with(['errorMessage' => 'You need to select an address to possessed']);
        }

        if (!Session::has('cart')) {
            return redirect()->route('shop.product.index', ['account' => $account])->with(['errorMessage' => 'Your Basket is empty']);
        } else {
            $validation_product_selection_size_color = ShopProductService::checkShoppingProductSizeColorSelections($account);
            if (!$validation_product_selection_size_color) {
                return redirect()
                    ->back()
                    ->withInput($data)
                    ->with(['errorMessage' => 'Please select the size and color of the product(s).']);
            }
        }

        $orders = $this->buyerRepository->storeOrder($account);
        removeCouponSessionCache();
        if (checkIfCouponFullyCoversOrder()) {
            ShopRepository::payCouponFreeForOrder($orders);
        } else {
            if ($data['payment_type'] == Transaction::MPESAGateAway) {
                list($transaction, $response) = $this->buyerRepository->storeTransaction($orders, $data, $account);
                if (isset(json_decode($response)->errorMessage)) {
                    Session::put('transaction_id', $transaction->transaction_id ?? null);
                    return redirect()->route('buyer.checkout.payment', ['account' => $account])->with(['errorMessage' => json_decode($response)->errorMessage]);
                }
            }
            if ($data['payment_type'] == Transaction::FlutterWaveGateAway) {
                list($MerchantRequestID, $redirectLink) = $this->buyerRepository->storeTransaction($orders, $data, $account);
                return Redirect::to($redirectLink);
            }
            if ($data['payment_type'] == Transaction::PayOnDeliveryGateAway) {
                $this->buyerRepository->storeTransaction($orders, $data, $account);
            }
            if ($data['payment_type'] == Transaction::SpotitGateAway) {
                $this->buyerRepository->storeTransaction($orders, $data, $account);
            }
            if ($data['payment_type'] == Transaction::PesapalGateAway) {
                list($MerchantRequestID, $redirectLink) = $this->buyerRepository->storeTransaction($orders, $data, $account);
                return redirect()->to($redirectLink);
            }
        }
        Invoice::syncOrders(session('invoice'), $orders->pluck('id'), $data['payment_type'] ?? null);
        return redirect()->route('buyer.checkout.confirm', ['account' => $account]);
    }

    public function confirmPage($account)
    {
        if ((!Session::has('orders_ids') || !Session::has('transaction_id')) && !(Session::get('payment_type') == 'PayOnDeliveryGateAway' || Session::get('payment_type') == 'freeCoupon')) {
            return redirect()->route('buyer.checkout.payment', ['account' => $account]);
        }
        $orders = Order::whereIn('id', Session::get('orders_ids', []))->get();
        if (!$orders->count()) {
            return redirect()->route('shop.product.index', ['account' => $account])->with(['errorMessage' => 'Your Basket is empty']);
        }
        Invoice::syncOrders(session('invoice'), $orders->pluck('id'), session('payment_type'));
        return view('shop::checkout.confirm', ['productsData' => $this->buyerRepository->getProductFromSession($account), 'orders' => $orders]);
    }

    public function checkOrderStatus()
    {
        if (Session::get('payment_type') == 'PayOnDeliveryGateAway' || Session::get('payment_type') == 'freeCoupon') {
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
        if (!Session::has('transaction_id') || !$transaction || $transaction->status == Transaction::TransactionFailed) {
            return response()->json(
                [
                    'transaction_status' => Transaction::TransactionFailed,
                ]
            );
        }
        if ($transaction->status == Transaction::PaymentSuccessfully) {
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

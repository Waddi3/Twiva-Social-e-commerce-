<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Shop\Entities\Shops;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceBusinessCreateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceBusinessDeleteCardRequest;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceBusinessUpdateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceInfluencerCreateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceInfluencerDeleteCardRequest;
use Modules\Shop\Http\Requests\ShopSite\ECommerce\ECommerceInfluencerUpdateCardRequest;
use Modules\User\Entities\User;

class ECommerceShopCartRedirectorController extends Controller
{
    use ResponseTrait;

    private $InfluencerShopCartController;
    private $BusinessShopCartController;

    public function __construct()
    {
        $this->InfluencerShopCartController = new ECommerceInfluencerCartController();
        $this->BusinessShopCartController = new ECommerceBusinessCartController();
    }

    private function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public function index()
    {
        [
            'products' => $influencer_products,
            'productsPrice' => $influencer_products_price,
            'shippingFees' => $influencer_shipping_fees,
            'qty' => $influencer_qty
        ] = $this->InfluencerShopCartController->index();

        [
            'products' => $business_products,
            'productsPrice' => $business_products_price,
            'shippingFees' => $business_shipping_fees,
            'qty' => $business_qty
        ]  =  $this->BusinessShopCartController->index();

        $products = $influencer_products->merge($business_products);
        $productsPrice = $influencer_products_price + $business_products_price;
        // $shippingFees = max($influencer_shipping_fees, $business_shipping_fees);
        $shippingFees = $influencer_shipping_fees;
        $qty = $influencer_qty + $business_qty;

        $totalWithShippingFees = round($productsPrice + $shippingFees);
        $transactionFees = round($totalWithShippingFees * Transaction::FlutterwaveTransactionFees);

        $cache_coupon = cache()->get(session('coupon_key'));
        if ($cache_coupon && $cache_coupon['coupon'] && ($cache_coupon['buyer']?->id == auth('buyer')->id() || session()->getId() == $cache_coupon['session_id'])) {
            $coupon_discount = $cache_coupon['coupon']->calculateDiscount($totalWithShippingFees);
            session([
                'coupon_with_shipping_fees' => $totalWithShippingFees - $coupon_discount,
                'coupon_discount' => $coupon_discount
            ]);
        } else {
            removeCouponSessionCache();
        }

        return response()->json([
            'products' => $products->count() ? view('front::components.views._cards', ['products' => $products])->render() : '',
            'count' => $qty,
            'total_with_shipping_fees' => $totalWithShippingFees,
            'coupon_with_shipping_fees' => isset($coupon_discount) ? $totalWithShippingFees - $coupon_discount : null,
            'transaction_fees' => $transactionFees,
            'products_price' => $productsPrice,
            'shipping_fees' => $shippingFees,
            'total_with_shipping_fees_and_transaction_fees' => $totalWithShippingFees + $transactionFees,
        ]);
    }

    public function create(Request $request)
    {
        Session::remove('address_id');
        Session::remove('city_id');
        $user_type = request('user_type');
        if ($user_type == User::InfluencerType ?? false) {
            $request = ECommerceInfluencerCreateCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->create($request);
        } else if ($user_type == User::BusinessType ?? false) {
            $request = ECommerceBusinessCreateCardRequest::createFrom($request);
            return $this->BusinessShopCartController->create($request);
        }
        abort(404);
    }

    public function update(Request $request)
    {
        $user_type = request('user_type');
        if ($user_type == User::InfluencerType ?? false) {
            $request = ECommerceInfluencerUpdateCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->update($request);
        } else if ($user_type == User::BusinessType ?? false) {
            $request = ECommerceBusinessUpdateCardRequest::createFrom($request);
            return $this->BusinessShopCartController->update($request);
        }
        abort(404);
    }


    public function delete(Request $request)
    {
        $user_type = request('user_type');
        if ($user_type == User::InfluencerType ?? false) {
            $request = ECommerceInfluencerDeleteCardRequest::createFrom($request);
            return $this->InfluencerShopCartController->delete($request);
        } else if ($user_type == User::BusinessType ?? false) {
            $request = ECommerceBusinessDeleteCardRequest::createFrom($request);
            return $this->BusinessShopCartController->delete($request);
        }
        abort(404);
    }

    public function updateAddress(Request $request)
    {
        if ($request->has('partner_id') && $request->partner_id !== null) {
            Session::put('partner_id', $request->partner_id);
        }

        if ($request->has('city_id') && $request->city_id !== null) {
            Session::put('city_id', $request->city_id);
            return response()->json([
                'status' => true,
                'message' => 'Shipping address updated'
            ]);
        }

        if ($request->has('address_id') && $request->address_id !== null) {
            try {
                Session::remove('city_id');
                Session::put('address_id', $request->address_id);
                return response()->json([
                    'status' => true,
                    'message' => 'Shipping address updated'
                ]);
            } catch (Exception $exception) {
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage()
                ]);
            }
        }

        if ($request->has('partner_id') && $request->partner_id !== null) {
            return response()->json([
                'status' => true,
                'message' => 'Partner updated'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Shipping address is required'
        ]);
    }
}

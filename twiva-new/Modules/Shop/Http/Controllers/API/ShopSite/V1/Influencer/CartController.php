<?php

namespace Modules\Shop\Http\Controllers\API\ShopSite\V1\Influencer;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\CreateCardRequest;
use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\DeleteCardRequest;
use Modules\Shop\Http\Requests\ShopSite\InfluencerSite\UpdateCardRequest;

class CartController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $cartsInfo = Session::get('cart') ?? [];
        $products = InfluencerShopProducts::whereIn('id', array_keys($cartsInfo))
            ->select('id', 'product_title', 'product_price', 'product_id')
            ->with([
                'productMainImage' => function ($query) {
                    return $query->select('id', 'shop_product_id', 'image_path');
                },
                'BusinessProductImages' => function ($BusinessProductImagesQuery) {
                    $BusinessProductImagesQuery->select('id', 'image', 'product_id');
                }
            ])->get();
        $productsPrice = 0;
        $qty = 0;
        $countOfHasShippingFeesProducts = 0;

        foreach ($products as $product) {
            $product->quantity = $cartsInfo[$product->id]['quantity'] ?? 0;
            $qty += $product->quantity;
            $productsPrice += $product->quantity * $product->product_price;
        }

        $cityId = Session::get('city_id');
        $addressId = Session::get('address_id');
        $partnerId = Session::get('partner_id');
        if ($cityId) {
            $shippingFees = ShippingService::calculateShippingFee($products, $cityId, $partnerId);
        } else {
            $address = ShippingAddress::find($addressId);
            $shippingFees = ShippingService::calculateShippingFee($products, $address?->city_id, $partnerId);
        }

        $totalWithShippingFees = round($productsPrice + $shippingFees);
        $transactionFees = round($totalWithShippingFees * Transaction::FlutterwaveTransactionFees);

        $cache_coupon = cache()->get(session('coupon_key'));
        if ($cache_coupon && $cache_coupon['coupon'] && ($cache_coupon['buyer']?->id == auth('buyer')->id()  || session()->getId() == $cache_coupon['session_id'])) {
            $coupon_discount = $cache_coupon['coupon']->calculateDiscount($totalWithShippingFees);
            session([
                'coupon_with_shipping_fees' => $totalWithShippingFees - $coupon_discount,
                'coupon_discount' => $coupon_discount
            ]);
        } else {
            removeCouponSessionCache();
        }

        return response()->json([
            'products' => $products->count() ? view('shop::components.views._cards', ['products' => $products])->render() : '',
            'count' => $qty,
            'total_with_shipping_fees' => $totalWithShippingFees,
            'coupon_with_shipping_fees' => isset($coupon_discount) ? $totalWithShippingFees - $coupon_discount : null,
            'transaction_fees' => $transactionFees,
            'products_price' => $productsPrice,
            'shipping_fees' => $shippingFees,
            'total_with_shipping_fees_and_transaction_fees' => $totalWithShippingFees + $transactionFees,
        ]);
    }

    public function create(CreateCardRequest $request)
    {

        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $should_redirect = $validated['should_redirect'] ?? false;
        unset($validated['should_redirect']);
        $cart = Session::get('cart');
        $influencer_product = InfluencerShopProducts::with('product:id,size,color')->select('id', 'product_title', 'product_price', 'product_id')
            ->where('id', $validated['product_id'])->first();

        updateInfoProductAliExpress($influencer_product?->product_id);
        if (!isset($validated['quantity'])) {
            $validated['quantity'] = 1;
        }
        if (!isset($validated['color_id']) && !isset($cart[$validated['product_id']]['color_id'])) {
            $color_id = null;
            if ($influencer_product) {
                if (count($influencer_product->product->colorDetails) and count($influencer_product->product->colorDetails) == 1) {
                    $color_id = $influencer_product->product->colorDetails->first()?->id;
                }
            }
            $validated['color_id'] = $color_id;
        }
        if (!isset($validated['size_id']) && !isset($cart[$validated['product_id']]['size_id'])) {
            $size_id = null;
            if ($influencer_product) {
                if (count($influencer_product->product->sizeDetails) and count($influencer_product->product->sizeDetails) == 1) {
                    $size_id = $influencer_product->product->sizeDetails->first()?->id;
                }
            }
            $validated['size_id'] = $size_id;
        }
        $cart[$validated['product_id']] = $validated;
        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id()) {
            auth('buyer')->user()->update([
               'cart' => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        if (isset($should_redirect)) {
            return response()->json(['code' => 200, 'redirect_url' => route('buyer.checkout.account', ['account' => $validated['shop_domain']])]);
        }
        return response()->json(['code' => 200]);
    }

    public function update(UpdateCardRequest $request)
    {
        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $cart = Session::get('cart');
        $cartProduct = $cart[$validated['product_id']] ?? null;
        if ($cartProduct && isset($validated['increase_multiple']) and $validated['increase_multiple']) {
            $validated['quantity'] = $validated['increase_multiple'];
        } else {
            if ($cartProduct && isset($validated['increase']) && $validated['increase']) {
                $validated['quantity'] = $cartProduct['quantity'] + 1;
            }
            if (isset($validated['increase']) && !$validated['increase']) {
                $validated['quantity'] = $cartProduct['quantity'] - 1;
            }
        }
        if (!isset($validated['increase'])) {
            if (isset($validated['increase_multiple']) and $validated['increase_multiple']) {
            } else {
                $validated['quantity'] = $cartProduct['quantity'] ?? 0;
            }
        }
        if (!isset($validated['color_id']) && !isset($cartProduct['color_id'])) {
            $validated['color_id'] = null;
        } else {
            $validated['color_id'] = ($validated['color_id'] ?? $cartProduct['color_id']) ?? false;
        }
        if (!isset($validated['size_id']) && !isset($cartProduct['size_id'])) {
            $validated['size_id'] = null;
        } else {
            $validated['size_id'] = ($validated['size_id'] ?? $cartProduct['size_id']) ?? false;
        }
        unset($validated['increase']);
        unset($validated['increase_multiple']);

        $cart[$validated['product_id']] = $validated;

        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id()) {
            auth('buyer')->user()->update([
                'cart' => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        $cart = Session::get('cart');
        return response()->json(['code' => 200, 'cart' => $cart]);
    }

    public function delete(DeleteCardRequest $request)
    {
        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $cart = Session::get('cart');
        unset($cart[$validated['product_id']]);
        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id()) {
            auth('buyer')->user()->update([
                'cart' => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        return response()->json(['code' => 200]);
    }
}

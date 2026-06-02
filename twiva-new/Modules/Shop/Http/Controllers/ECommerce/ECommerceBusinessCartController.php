<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\ShippingAddress;
use Modules\Order\Http\Services\ShippingService;
use Modules\User\Entities\User;

class ECommerceBusinessCartController extends Controller {
    use ResponseTrait;

    public function index()
    {
        $cartsInfo = Session::get('cart') ?? [];
        $products_ids = collect($cartsInfo)->where('user_type', User::BusinessType)->pluck('product_id');
        $products = Product::whereIn('id', $products_ids)
            ->select('id', 'user_id', 'name', 'price', 'retail_price', 'discount', 'has_shipping_fees')
            ->with(['productImages' => function ($query)
            {
                return $query->select('id', 'image', 'is_cover_pic');
            }])->get();
        $productsPrice = 0;
        $qty = 0;

        foreach ($products as $product)
        {
            $product->quantity = $cartsInfo[$product->id]['quantity'];
            $qty += $product->quantity;
            $price = $product->discount ?? $product->retail_price;
            $productsPrice += $product->quantity * $price;
        }

        $addressId = Session::get('address_id');
        $address = ShippingAddress::find($addressId);
        $partnerId = Session::get('partner_id');
        $shippingFees = ShippingService::calculateShippingFee($products, $address?->city_id, $partnerId, true);


        return ['products' => $products, 'productsPrice' => $productsPrice, 'shippingFees' => $shippingFees, 'qty' => $qty];
    }

    public function create($request)
    {
        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $should_redirect = $validated['should_redirect'] ?? false;
        unset($validated['should_redirect']);
        $cart = Session::get('cart');

        $product = Product::select('id', 'size', 'color')
            ->where('id', $validated['product_id'])->first();

        if (!isset($validated['quantity']))
        {
            $validated['quantity'] = 1;
        }
        if (!isset($validated['color_id']) && !isset($cart[$validated['product_id']]['color_id']))
        {
            $color_id = null;
            if ($product)
            {
                if (count($product->colorDetails) and count($product->colorDetails) == 1)
                {
                    $color_id = $product->colorDetails->first()?->id;
                }
            }
            $validated['color_id'] = $color_id;
        }
        if (!isset($validated['size_id']) && !isset($cart[$validated['product_id']]['size_id']))
        {
            $size_id = null;
            if ($product)
            {
                if (count($product->sizeDetails) and count($product->sizeDetails) == 1)
                {
                    $size_id = $product->sizeDetails->first()?->id;
                }
            }
            $validated['size_id'] = $size_id;
        }
        $cart[$validated['product_id']] = $validated;
        $cart['user_type'] = User::BusinessType;
        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id())
        {
            auth('buyer')->user()->update([
                'cart'                   => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        if (isset($should_redirect))
        {
            return response()->json(['code' => 200, 'redirect_url' => route('e-commerce.buyer.checkout.account')]);
        }
        return response()->json(['code' => 200]);
    }

    public function update($request)
    {
        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $cart = Session::get('cart');
        $cartProduct = $cart[$validated['product_id']];
        if (isset($validated['increase_multiple']) and $validated['increase_multiple'])
        {
            $validated['quantity'] = $validated['increase_multiple'];
        }
        else
        {
            if (isset($validated['increase']) && $validated['increase'])
            {
                $validated['quantity'] = $cartProduct['quantity'] + 1;
            }
            if (isset($validated['increase']) && !$validated['increase'])
            {
                $validated['quantity'] = $cartProduct['quantity'] - 1;
            }
        }
        if (!isset($validated['increase']))
        {
            if (isset($validated['increase_multiple']) and $validated['increase_multiple'])
            {
            }
            else
            {
                $validated['quantity'] = $cartProduct['quantity'];
            }
        }
        if (!isset($validated['color_id']) && !isset($cartProduct['color_id']))
        {
            $validated['color_id'] = null;
        }
        else
        {
            $validated['color_id'] = ($validated['color_id'] ?? $cartProduct['color_id']) ?? false;
        }
        if (!isset($validated['size_id']) && !isset($cartProduct['size_id']))
        {
            $validated['size_id'] = null;
        }
        else
        {
            $validated['size_id'] = ($validated['size_id'] ?? $cartProduct['size_id']) ?? false;
        }
        unset($validated['increase']);

        $cart[$validated['product_id']] = $validated;
        $cart['user_type'] = User::BusinessType;

        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id())
        {
            auth('buyer')->user()->update([
                'cart'                   => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        $cart = Session::get('cart');
        return response()->json(['code' => 200, 'cart' => $cart]);
    }

    public function delete($request)
    {
        $validated = $request->validate($request->rules());
        $validated = data_get($validated, null, null);
        $cart = Session::get('cart');
        unset($cart[$validated['product_id']]);
        Session::put('cart', $cart);
        Session::save();
        if (auth('buyer')->id())
        {
            auth('buyer')->user()->update([
                'cart'                   => $cart,
                'cart_last_updated_date' => now(),
            ]);
        }
        return response()->json(['code' => 200]);
    }
}

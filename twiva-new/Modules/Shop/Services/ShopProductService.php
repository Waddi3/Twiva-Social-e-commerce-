<?php

namespace Modules\Shop\Services;

use Illuminate\Support\Facades\Session;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\User;

class ShopProductService
{
    public static function checkShoppingProductSizeColorSelections($account, $cart = null): bool
    {
        if (is_null($cart)) {
            $cart = session('cart');
        }
        $products = self::getProductFromSession($account, $cart);
        $validation = true;
//        dd($products->pluck('size'), $products->pluck('color'), $cart);
        foreach ($products as $product) {
            if ($product instanceof InfluencerShopProducts) {
                $product_temp = clone $product->product;
            } else {
                $product_temp = clone $product;
            }
            if (is_null($product_temp->size) and is_null($cart[$product->id]['size_id'])) {
            } elseif ($product_temp->size and ($cart[$product->id]['size_id'] ?? false)){
                if(!in_array($cart[$product->id]['size_id'], explode(',', $product_temp->size))){
                    $validation = false;
                }
            } else {
                $validation = false;
            }
            if (is_null($product_temp->color) and is_null($cart[$product->id]['color_id'])) {
            } elseif ($product_temp->color and ($cart[$product->id]['color_id'] ?? false)){
                if(!in_array($cart[$product->id]['color_id'], explode(',', $product_temp->color))){
                    $validation = false;
                }
            } else {
                $validation = false;
            }
//            dd($product_temp->color, $cart[$product->id]['color_id'], $validation);
        }
        return $validation;
    }

    public static function getProductFromSession($account, $cart = null)
    {
        if (self::shopData($account)?->user?->type == User::InfluencerType ?? false) {
            return self::getInfluencerProductsFromSession($cart);
        } else if (self::shopData($account)?->user?->type == User::BusinessType ?? false) {
            return self::getBusinessProductsFromSession($cart);
        }
    }

    public static function shopData($account)
    {
        return Shops::select('id', 'user_id')->with('user')->where('domain', $account)->first();
    }

    public static function getInfluencerProductsFromSession($cart = null)
    {
        $cartsInfo = self::getProductIds($cart);
        return InfluencerShopProducts::select('id', 'product_title', 'product_price', 'product_id')
            ->whereIn('id', array_keys($cartsInfo))
            ->with(['product:id,user_id,price,size,color,retail_price,discount'])
            ->get();
    }

    public static function getBusinessProductsFromSession($cart = null)
    {
        $cartsInfo = self::getProductIds($cart);
        return Product::whereIn('id', array_keys($cartsInfo))
            ->select('id', 'name', 'price', 'retail_price', 'discount', 'user_id', 'size', 'color', 'has_shipping_fees')
            ->get();
    }

    private static function getProductIds(mixed $cart)
    {
        if ($cart) {
            $cartsInfo = Session::get('cart') ?? $cart;
        } else {
            $cartsInfo = [];
            $cartsInfo_color = collect(session('cart'))->whereNull('color_id')->toArray();
            $cartsInfo_size = collect(session('cart'))->whereNull('size_id')->toArray();
            foreach ($cartsInfo_color as $product_id => $value) {
                if (!array_key_exists($product_id, $cartsInfo)) {
                    $cartsInfo[$product_id] = $value;
                }
            }
            foreach ($cartsInfo_size as $product_id => $value) {
                if (!array_key_exists($product_id, $cartsInfo)) {
                    $cartsInfo[$product_id] = $value;
                }
            }

        }
        return $cartsInfo;
    }

}

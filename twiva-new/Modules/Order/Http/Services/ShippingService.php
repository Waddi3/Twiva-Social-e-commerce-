<?php

namespace Modules\Order\Http\Services;

use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\ShippingRate;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\User\Entities\User;

class ShippingService
{
    private const DEFAULT_FEE = 350;

    /**
     * @deprecated
     */
    public static function shippingFee($products, $addressId = null, $isBusiness = false)
    {
        if (is_null($addressId)) {
            return 0;
        }
        $shippingFee = null;
        if ($products->isEmpty()) {
            return static::DEFAULT_FEE;
        }
        foreach ($products as $product) {
            if ($isBusiness) {
                $has_shipping_fee = $product?->has_shipping_fees;
            } else {
                $has_shipping_fee = $product->productDetails?->has_shipping_fees;
            }
            if ($has_shipping_fee) {
                if ($isBusiness) {
                    $sourceCityId = $product->user?->businessDetails?->city;
                } else {
                    $sourceCityId = $product->productDetails?->user?->businessDetails?->city;
                }

                if (!$sourceCityId) {
                    $shippingFee = max(static::DEFAULT_FEE, $shippingFee);
                } else {
                    $shippingRate = ShippingRate::where('source_city_id', $sourceCityId)
                        ->where('destination_city_id', $addressId)
                        ->first();

                    if ($shippingRate) {
                        $shippingFee = max($shippingRate->rate, $shippingFee);
                    }
                }
            }
        }

        return is_null($shippingFee) ? null :($shippingFee == 0 ? static::DEFAULT_FEE : (int)$shippingFee);
    }

    public static function calculateShippingFee($products, $addressId = null, $partnerId = null , $isBusiness = false)
    {
        if (is_null($addressId)) {
            return 0;
        }
        if ($products->isEmpty()) {
            return 0;
        }
        $list = [];
        foreach ($products as $index => $product) {
            $productShippingFee = null;
            $businessId = 0;
            if ($product instanceof InfluencerShopProducts) {
                $businessId = $product->productDetails?->business->id;
                $businessShipping = $product->productDetails?->business->businessShipping;
            } else {
                $businessId = $product->business->id;
                $businessShipping = $product->business->businessShipping;
            }
            if( in_array($businessId, $list) ) {
                continue;
            }
            $has_shipping_fees = $businessShipping->has_shipping_fees;
            $shipping_by_partner = $businessShipping->shipping_by_partner;
            $sourceCityId = null;
            if ($shipping_by_partner) {

                if ($product instanceof InfluencerShopProducts) {
                    $sourceCityId = $product->productDetails?->user?->businessDetails?->city;
                } else {
                    $sourceCityId = $product->user?->businessDetails?->city;

                }
                if (!$sourceCityId) {
                    $productShippingFee = static::DEFAULT_FEE;//max(static::DEFAULT_FEE, $productShippingFee);
                } else {
                    $shippingRate = ShippingRate::where('source_city_id', $sourceCityId)
                        ->where('destination_city_id', $addressId)
                        ->where('partner_id', $partnerId)
                        ->first();

                    if ($shippingRate) {
                        $productShippingFee = $shippingRate->rate; //max($shippingRate->rate, $productShippingFee);
                    }
                    else {
                        $productShippingFee = static::DEFAULT_FEE;
                    }
                }
            }
            else {
                // Calc has shipping fee
                if( $has_shipping_fees ) {
                    $productShippingFee = 0;
                }
                else {
                    $productShippingFee = static::DEFAULT_FEE;
                }
            }

            $list[$businessId] = $productShippingFee;
        }

        $finalShippingFee = 0;
        foreach( $list as $fee) {
            $finalShippingFee += (int)$fee;
        }

        return $finalShippingFee;
    }
}

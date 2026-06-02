<?php

namespace Modules\Coupon\Http\Controllers\Buyer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coupon\Entities\Coupon;
use Modules\Shop\Http\Controllers\ECommerce\ECommerceShopCartRedirectorController;
use Modules\Shop\Http\Controllers\ShopCartRedirectorController;

class BuyerCouponController extends Controller
{
    public function __construct()
    {
    }

    public function checkCoupon($account = null)
    {
        $is_validate = false;
        $coupon_discount = 0;
        $coupon_code = request('coupon_code');
        // $account = request('account');
        $buyer = auth('buyer')->user();
        // dd($coupon_code, $buyer);
        $ttl = now()->addMinutes(10);
        // $ttl = now()->addSeconds(30);
        $cart = session('cart');
        if ($account) {
            $cart = json_decode((new ShopCartRedirectorController())->index(removeDotFromSubDomain($account))->getContent());
        } else {
            $cart = json_decode((new ECommerceShopCartRedirectorController())->index()->getContent());
        }
        $key = "coupon-$coupon_code"; // -{$buyer?->id}
        // dd(cache()->get($key));
        if (is_null(cache()->get($key))) {
            ['coupon' => $coupon, 'buyer' => $buyer, 'session_id' => $session_id] = cache()->remember($key, $ttl, function () use ($coupon_code, $buyer, $cart) {
                return [
                    'coupon' => Coupon::validateCouponForBuyer($coupon_code, $buyer, $cart->total_with_shipping_fees),
                    'buyer' => $buyer,
                    'session_id' => session()->getId(),
                ];
            });
            if ($coupon) {
                if (cache()->get(session('coupon_key'))) {
                    removeCouponSessionCache();
                }
                if (isset($cart->total_with_shipping_fees)) {
                    $coupon_discount = $coupon->calculateDiscount($cart->total_with_shipping_fees);
                }
                session([
                    'coupon_code' => $coupon->code,
                    'coupon_key' => $key,
                    'coupon_with_shipping_fees' => $cart->total_with_shipping_fees - $coupon_discount,
                    'coupon_discount' => $coupon_discount
                ]);
                $is_validate = true;
            } else {
                cache()->forget($key);
            }
        }
        $error_message = '';
        if(!$is_validate){
            $error_message = Coupon::validateMessageCouponForBuyer($coupon_code, $buyer, $cart->total_with_shipping_fees);
        }
        return response()->json([
            'is_validate' => $is_validate,
            'error_message' => $error_message,
            'coupon_code' => $coupon_code,
            'coupon_with_shipping_fees' => $cart->total_with_shipping_fees - $coupon_discount,
            'coupon_discount' => $coupon_discount
        ]);
    }

    public function spinWheel($account = null)
    {
        $spin_wheel = Coupon::getChanceWith60TryAgain(0, 2000,  200);
        $get_element = Coupon::choseRandomElement($spin_wheel);
        $winning_item_index = Coupon::getIndexFromRandomElement($get_element, $spin_wheel);
        // $random_percentage = mt_rand(5, 20);


        $tried_today = Coupon::spinWheelBuyerToday();
        // dd($tried_today, $tried_today < config('twiva.spin-wheel.times-every-day'));
        $coupon_code = null;
        // dd($tried_today);
        $timesEveryDay = (int)config('twiva.spin-wheel.times-every-day') ? (int)config('twiva.spin-wheel.times-every-day') : 10;
        if ($tried_today < $timesEveryDay) {

            if ($get_element) {
                $buyer_id = auth('buyer')->id();

                $coupon = Coupon::create([
                    'name' => "spin-wheel",
                    'code' => Coupon::generateUniqueCode(),
                    'max_usage' => 1,
                    'type' => Coupon::TYPE_FIXED,
                    'amount' => $get_element,
                    'expiry_at' => now()->endOfDay(),
                    'is_active' => 1,
                    'by_spin_wheel' => 1,
                    'buyer_id' => $buyer_id,
                ]);
                $coupon_code = $coupon->code;
            }
            session(['try-spin-wheel-today' => session('try-spin-wheel-today') + 1]);
        }

        return response()->json([
            'winning_item_index' => $winning_item_index,
            'coupon_code' => $coupon_code,
            'tried_today' => $tried_today + 1,
            'spin_wheel_times_every_day' => $timesEveryDay,
        ]);
    }
}

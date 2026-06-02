<?php


namespace Modules\User\Repository;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Buyer\Entities\Buyer;
use Modules\Notification\Notifications\User\RegistrationWithUserReferralCodeNotification;
use Modules\Order\Entities\Order;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Modules\User\Entities\UserReferralCode;

class UserRepository
{
    public static function createReferralCode()
    {
        $referral_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 8);
        return self::checkReferralCode($referral_code);

    }

    private static function checkReferralCode($referral_code)
    {
        $is_referral_code_exists = User::where('referral_code', $referral_code)->exists();
        if ($is_referral_code_exists)
        {
            $referral_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 8);
            self::checkReferralCode($referral_code);
        }
        return $referral_code;
    }

    public static function rewardInvitor($invitor_referral_code, $influencer): void
    {
        $invitor = User::where('referral_code', $invitor_referral_code)->first();
        if ($invitor)
        {
            UserReferralCode::create(
                [
                    'invited_type' => User::InfluencerType,
                    'invited_id' => $influencer->id,
                    'invitor_type' => $invitor->type,
                    'invitor_id' => $invitor->id,
                    'points' => 5,
                ]);
            $invitor->notify(new RegistrationWithUserReferralCodeNotification());
            referralPrivilegesToUser($invitor);
        }
    }

    public static function createOTP($user, $type, $generate = true)
    {
        $otpCode = random_int(10000, 99999);

        // Buyer
        if( get_class($user) == Buyer::class ) {
            $user->OTP()->updateOrCreate(
                [],
                ['code' => $otpCode,]
            );
        }
        // Business, Influencer and etc
        else {
            $user->OTP()->create([
                'otp' => $otpCode,
                'expiry_date' => Carbon::now()->addMinutes(10),
                'type' => $type,
            ]);
        }
        return $otpCode;
    }

    public static function topInfluencers($userCount = 5)
    {
        $influencers = [];
        $topOrdersShop = Order::with('shop.user')
            ->where('order_status', Order::DELIVERED_STATUS)
            ->whereHas('shop', function ($shopQuery) {
                $shopQuery->whereHas('user', function ($userQuery) {
                    $userQuery->where('type', User:: InfluencerType);
                });
            })
            ->select('shop_id', DB::Raw("sum(order_amount) as amounts"), DB::Raw("count(id) as orders_count"))
            ->groupBy('shop_id')
            ->orderBy('amounts', 'DESC')
            ->take(5)
            ->get();

        foreach ($topOrdersShop as $order)
        {
            $order->shop->user->sellAmount = $order->amounts;
            $order->shop->user->orders_count = $order->orders_count;
            $influencers[] = $order->shop->user;
        }
        return $influencers;
    }

    public static function topBusiness($userCount = 5)
    {
        $businesses = [];
        $topOrdersShop = Order::with('business')
            ->select('business_id', DB::Raw("sum(order_amount) as amounts"), DB::Raw("count(id) as orders_count"))
            ->where('order_status', Order::DELIVERED_STATUS)
            ->groupBy('business_id')
            ->orderBy('amounts', 'DESC')
            ->take(5)
            ->get();
        foreach ($topOrdersShop as $order)
        {
            if ($order->business)
            {
                $order->business->sellAmount = $order->amounts;
                $order->business->orders_count = $order->orders_count;
                $businesses[] = $order->business;
            }

        }
        return $businesses;

    }

    public static function storeFCMTokens($device_token, User|Authenticatable|null $user): void
    {
        if (($device_token ?? false) && (!in_array($device_token, $user->fcm_token ?? [])))
        {
            $tokens = $user->fcm_token;
            $tokens[] = $device_token;
            $user->update(['fcm_token' => $tokens]);
        }
    }

    public static function createShop($user)
    {
        $shop = new Shops();
        $shop->user_id = $user->id;
        $shop->shop_name = $user->name;
        $shop->shop_logo_path = str_replace([asset('storage/images/products/'), asset('storage/images/products/') . '/'], '', $user->profile_image);
        $shop->shop_cover_image_path = '1655893364.jpeg';
        $shop->is_active = 1;
        $shop->domain = $shop->generateDomainFromShopName($user->name, true);
        $shop->save();
    }


}

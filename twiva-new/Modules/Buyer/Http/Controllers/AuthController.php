<?php


namespace Modules\Buyer\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\Notification\Notifications\Buyer\VerifyPhoneNumberNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\Shop\Repositories\Influencer\BuyerRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository;
use Modules\User\Entities\Otp;
use Modules\User\Repository\UserRepository;
use NotificationChannels\Twilio\TwilioChannel;

class AuthController
{

    private BuyerRepository $buyerRepository;

    public function __construct(BuyerRepository $buyerRepository)
    {
        $this->buyerRepository = $buyerRepository;
    }

    public function loginPage()
    {
        Session::remove('shop_redirect_to');
        $from = url()->previous();
        if (!Str::contains($from, 'login') && !Str::contains($from, 'register')) {
            Session::put('shop_redirect_to', $from);
        }

        return view('shop::auth.login');
    }


    public function registerPage($account)
    {
        Session::remove('shop_redirect_to');
        $from = url()->previous();
        if (!Str::contains($from, 'login') && !Str::contains($from, 'register')) {
            Session::put('shop_redirect_to', $from);
        }
        return view('shop::auth.register');
    }

    public function forgetPasswordPage($account)
    {
        return view('shop::auth.forget-password');
    }

    public function logout($account)
    {
        Auth::guard('buyer')->logout();
        return redirect()->route('shop.product.index', ['account' => $account]);
    }

    public function resetPasswordPage()
    {
        $token = DB::table('password_resets')->where('token', request('token'))->first();
        $expire = Carbon::parse($token?->expire);
        $isValid = $token && now()->lt($expire);
        if ($isValid) {
            return view('shop::auth.reset-password');
        }
        abort(404);
    }

    public function verifyPage($account)
    {
        $buyer = Auth::guard('buyer')->user();
        if (!$buyer || $buyer->is_verified) {
            if (Session::has('shop_redirect_to')) {
                return redirect(Session::get('shop_redirect_to'));
            }
            return redirect()->route('shop.product.index', ['account' => $account]);
        }

        $submit = true;
        $remainTime = 0;
        $otp = $buyer->OTP()->first();

        if( $otp ) {
            $spentTime = Carbon::parse($otp->last_attempt)->diffInMinutes(Carbon::now());

            // Resend code after 15 minues
            if( $spentTime >= 15 ) {
                $otp->delete();
                $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
                $buyer->notify(new SendOTPViaMobileNotification($otp));
            }

            if ($otp->attempts >= 3 && $spentTime <= 15) {
                $remainTime = 15 - $spentTime;
                $submit = false;
            }
        }
        else {
            $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
            $buyer->notify(new SendOTPViaMobileNotification($otp));
        }

        if ( Session::has('invoice') ) {
            Auth::guard('buyer')->loginUsingId(session('invoice')['buyer_id']);
            $buyer = auth('buyer')->user();
            session(['shop_redirect_to' => route('buyer.checkout.shipping', ['account' => $account])]);
            return view('buyer::verify.verify', ['productsData' => $this->buyerRepository->getProductFromSession($account), 'buyer' => $buyer]);
        }
        return view('shop::auth.verify', [
            'submit' => $submit,
            'remainTime' => $remainTime
        ]);
    }

}

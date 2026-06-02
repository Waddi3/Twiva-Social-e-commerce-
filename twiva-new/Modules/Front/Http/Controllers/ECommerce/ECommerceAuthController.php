<?php

namespace Modules\Front\Http\Controllers\ECommerce;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Http\Requests\ForgetPasswordRequest;
use Modules\Buyer\Http\Requests\LoginRequest;
use Modules\Buyer\Http\Requests\PhoneNumberVerifyRequest;
use Modules\Buyer\Http\Requests\RegisterRequest;
use Modules\Home\Entities\Category;
use Modules\Notification\Notifications\Buyer\ResetPasswordNotification;
use Modules\Notification\Notifications\Buyer\ResetPasswordViaSmsNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\Shop\Repositories\ECommerce\ECommerceInfluencerBuyerRepository;
use Modules\User\Entities\Otp;
use Modules\User\Repository\UserRepository;

class ECommerceAuthController extends Controller {
    private ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository;

    public function __construct(ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository)
    {
        $this->e_commerce_buyer_repository = $e_commerce_buyer_repository;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $loginInWith = array_key_exists('mobile', $data) ? 'phone_number' : 'email';
        $loginInWithKey = array_key_exists('mobile', $data) ? 'mobile' : 'email';
        $loginAttempt = Auth::guard('buyer')->attempt([
            $loginInWith  => $data[$loginInWithKey],
            'password'    => $data['password'],
            'is_external' => 0
        ]);

        if ($loginAttempt)
        {
            if (auth('buyer')->user()->cart && !Session::has('cart'))
            {
                Session::put('cart', auth('buyer')->user()->cart);
                Session::save();
            }
            if (Str::contains(url()->previous(), 'checkout/account'))
            {
                return redirect()->route('e-commerce.buyer.checkout.shipping');
            }
            if (Session::has('shop_redirect_to'))
            {
                return redirect(Session::get('shop_redirect_to'));
            }
            return redirect()->route('ecommerce.index');
        }
        return redirect()->back()->withErrors(['email' => 'Email and password are not match !']);
    }

    public function loginPage()
    {
        Session::remove('shop_redirect_to');
        $from = url()->previous();
        if (!Str::contains($from, 'login') && !Str::contains($from, 'register'))
        {
            Session::put('shop_redirect_to', $from);
        }

        return view('front::auth.login');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        unset($data['password_confirmation']);
        $data['phone_code'] = str_replace('+', '', explode('-', $data['mobile'])[0]);
        $data['phone_number'] = explode('-', $data['mobile'])[1];
        $data['phone_number'] = str_replace(' ', '', $data['phone_number']);
        unset($data['mobile']);

        $data['class'] = Buyer::INDIVIDUAL;
        $data['rep_name'] = $data['name'];
        $data['rep_phone_code'] = $data['phone_code'];
        $data['rep_phone_number'] = $data['phone_number'];

        // Remove buyer if is unverified
        $buyer = Buyer::where('phone_number', $data['phone_number'])
            ->where('phone_code', $data['phone_code'])
            ->where('is_external', 0)
            ->first();
        if ($buyer)
        {
            $buyer->update($data);
        }
        else
        {
            $buyer = Buyer::create($data);
        }
        $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
        $buyer->notify(new SendOTPViaMobileNotification($otp));
        Auth::guard('buyer')->loginUsingId($buyer->id);
        return redirect()->route('e-commerce.buyer.verify');
    }


    public function registerPage()
    {
        Session::remove('shop_redirect_to');
        $from = url()->previous();
        if (!Str::contains($from, 'login') && !Str::contains($from, 'register'))
        {
            Session::put('shop_redirect_to', $from);
        }
        return view('front::auth.register');
    }

    public function forgetPasswordPage()
    {
        return view('front::auth.forget-password');
    }

    public function logout()
    {
        Auth::guard('buyer')->logout();
        return redirect()->route('ecommerce-page');
    }

    public function resetPasswordPage()
    {
        $token = DB::table('password_resets')->where('token', request('token'))->first();
        $expire = Carbon::parse($token?->expire);
        $isValid = $token && now()->lt($expire);
        if ($isValid)
        {
            return view('front::auth.reset-password');
        }
        abort(404);
    }

    public function verifyPage()
    {
        $buyer = Auth::guard('buyer')->user();
        if (!$buyer || $buyer->is_verified)
        {
            if (Session::has('shop_redirect_to'))
            {
                return redirect(Session::get('shop_redirect_to'));
            }
            return redirect()->route('ecommerce.index');
        }

        $submit = true;
        $remainTime = 0;
        $otp = $buyer->OTP()->first();

        if ($otp)
        {
            $spentTime = Carbon::parse($otp->last_attempt)->diffInMinutes(Carbon::now());

            // Resend code after 15 minues
            if ($spentTime >= 15)
            {
                $otp->delete();
                $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
                $buyer->notify(new SendOTPViaMobileNotification($otp));
            }

            if ($otp->attempts >= 3 && $spentTime <= 15)
            {
                $remainTime = 15 - $spentTime;
                $submit = false;
            }
        }
        else
        {
            $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
            $buyer->notify(new SendOTPViaMobileNotification($otp));
        }

        if (Session::has('invoice'))
        {
            Auth::guard('buyer')->loginUsingId(session('invoice')['buyer_id']);
            $buyer = auth('buyer')->user();
            session(['shop_redirect_to' => route('e-commerce.buyer.checkout.shipping')]);
            return view('buyer::verify.verify', ['productsData' => $this->e_commerce_buyer_repository->getProductFromSession(), 'buyer' => $buyer]);
        }
        return view('front::auth.verify', [
            'submit'     => $submit,
            'remainTime' => $remainTime
        ]);
    }

    public function verify(PhoneNumberVerifyRequest $request)
    {
        $buyer = auth('buyer')->user();
        $otp = $buyer->OTP()->orderBy('id', 'desc')->first();
        if (!$otp)
        {
            return redirect()->back()->withErrors(["code" => "OTP code not found"]);
        }
        else
        {
            if ($otp->code == $request->code)
            {
                $buyer->is_verified = 1;
                $buyer->save();
                $otp->delete();
                if (Session::has('shop_redirect_to'))
                {
                    return redirect(Session::get('shop_redirect_to'));
                }
                return redirect()->route('ecommerce.index');
            }
        }
        return redirect()->back()->withErrors(["code" => "Invalid OTP code"]);
    }

    public function forgetPasswordAction(ForgetPasswordRequest $request)
    {
        $token = Str::random(39);
        $data = $request->validated();
        $email = $data['email'] ?? $data['mobile'];

        $password_reset = DB::table('password_resets')->insert(
            [
                'user_id'    => 0, 'email' => $email,
                'token'      => $token,
                'created_at' => now(),
                'expire'     => now()->addMinutes(30),
            ]
        );

        if (array_key_exists('mobile', $data))
        {
            $buyer = Buyer::where(['phone_number' => $data['mobile']])->first();
            // by sms
            $url = route('e-commerce.buyer.reset-password', ['token' => $token]);
            $buyer->notify(new ResetPasswordViaSmsNotification($url));
        }
        else
        {
            $buyer = Buyer::where(['email' => $email])->first();
            $data['name'] = $buyer->name;
            $data['token'] = $token;
            $data['account'] = null;
            // by email
            $buyer->notify(new  ResetPasswordNotification($data));
        }


        return back()->with(['message' => 'Reset Link sent !']);
    }

}

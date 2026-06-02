<?php

namespace Modules\Buyer\Http\Controllers\ECommerce;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Http\Requests\ChangePasswordRequest;
use Modules\Buyer\Http\Requests\ForgetPasswordRequest;
use Modules\Buyer\Http\Requests\LoginRequest;
use Modules\Buyer\Http\Requests\PhoneNumberVerifyRequest;
use Modules\Buyer\Http\Requests\RegisterRequest;
use Modules\Buyer\Http\Requests\ResetPasswordAfterForgetRequest;
use Modules\Notification\Notifications\Buyer\ResetPasswordNotification;
use Modules\Notification\Notifications\Buyer\ResetPasswordViaSmsNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\User\Entities\Otp;
use Modules\User\Repository\UserRepository;

class ECommerceBuyerController extends Controller {
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
        return redirect()->route('e-commerce.buyer.verify.action');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $loginInWith = array_key_exists('mobile', $data) ? 'phone_number' : 'email';
        $loginInWithKey = array_key_exists('mobile', $data) ? 'mobile' : 'email';
        $loginAttempt = Auth::guard('buyer')->attempt([
                $loginInWith => $loginInWithKey,
                'password' => $data['password'],
                'is_external' => 0
            ]);
        if ($loginAttempt)
        {
            if (auth('buyer')->user()->cart && !Session::has('cart'))
            {
                Session::put('cart', auth('buyer')->user()->cart);
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
            $data['account'] = request('account');
            // by email
            $buyer->notify(new  ResetPasswordNotification($data));
        }


        return back()->with(['message' => 'Reset Link sent !']);
    }

    public function verify(PhoneNumberVerifyRequest $request)
    {
        $buyer = Auth::guard('buyer')->user();
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

    public function settingsPage()
    {
        return view('front::buyer.settings');
    }

    public function changePasswordPage()
    {
        return view('front::buyer.password.edit');
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $buyer = Auth::guard('buyer')->user();
        if (!Hash::check($data['current_password'], $buyer->getAuthPassword()))
        {
            return redirect()->back()->withErrors(["current_password" => "current password is not correct"]);
        }
        $buyer->update(["password" => bcrypt($data['password'])]);
        return redirect()->back()->with(['message' => 'Password has been changed']);
    }

    public function resetPasswordAction(ResetPasswordAfterForgetRequest $request)
    {
        $data = $request->validated();
        $token = DB::table('password_resets')->where('token', request('token'))->first();
        $expire = Carbon::parse($token?->expire);
        $isValid = $token && now()->lt($expire);
        if ($isValid)
        {
            $buyer = Buyer::where('email', $token->email)->first();
            if (!$buyer)
            {
                $buyer = Buyer::where('phone_number', $token->email)->first();
            }
            if (!$buyer)
            {
                abort(404);
            }
            $buyer->update(["password" => bcrypt($data['password'])]);
            return redirect()->route('ecommerce.index');
        }
        abort(403);
    }

    public function resendCode()
    {
        $buyer = auth('buyer')->user();
        //TwiloVerifyServices::sendVerificationCode($buyer->mobile);
        if (is_null($buyer))
        {
            return response()->json(['data' => 'error']);
        }
        $buyer->OTP()->delete();
        $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
        $buyer->notify(new SendOTPViaMobileNotification($otp));

        return response()->json(['data' => 'send sms']);
    }
}

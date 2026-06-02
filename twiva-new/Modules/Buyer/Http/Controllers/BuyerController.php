<?php

namespace Modules\Buyer\Http\Controllers;

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

class BuyerController extends Controller
{
    public function register($account, RegisterRequest $request)
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
        if ($buyer) {
            $buyer->update($data);
        } else {
            $buyer = Buyer::create($data);
        }
        // $buyer = \Modules\Buyer\Entities\Buyer::find(100);
        // $otp = \Modules\User\Repository\UserRepository::createOTP($buyer, \Modules\User\Entities\Otp::FOR_MOBILE);
        $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
        $buyer->notify(new SendOTPViaMobileNotification($otp));
        Auth::guard('buyer')->loginUsingId($buyer->id);
        return redirect()->route('buyer.verify', ['account' => $account]);
    }

    public function login($account, LoginRequest $request)
    {
        $data = $request->validated();
        if (array_key_exists('mobile', $data)) {
            if (Auth::guard('buyer')->attempt(['phone_number' => $data['mobile'], 'password' => $data['password'], 'is_external' => 0])) {
                if (Str::contains(url()->previous(), 'checkout/account')) {
                    return redirect()->route('buyer.checkout.shipping', ['account' => $account]);
                }
                if (Session::has('shop_redirect_to')) {
                    return redirect(Session::get('shop_redirect_to'));
                }
                return redirect()->route('shop.product.index', ['account' => $account]);
            }
        } else {
            if (Auth::guard('buyer')->attempt(['email' => $data['email'], 'password' => $data['password'], 'is_external' => 0])) {
                if (Str::contains(url()->previous(), 'checkout/account')) {
                    return redirect()->route('buyer.checkout.shipping', ['account' => $account]);
                }
                if (Session::has('shop_redirect_to')) {
                    return redirect(Session::get('shop_redirect_to'));
                }
                return redirect()->route('shop.product.index', ['account' => $account]);
            }
        }
        return redirect()->back()->withErrors(['email' => 'Email and password are not match !']);
    }

    public function forgetPasswordAction($account, ForgetPasswordRequest $request)
    {
        $token = Str::random(39);
        $data = $request->validated();
        $email = $data['email'] ?? $data['mobile'];

        $password_reset = DB::table('password_resets')->insert(
            [
                'user_id' => 0, 'email' => $email,
                'token' => $token,
                'created_at' => now(),
                'expire' => now()->addMinutes(30),
            ]
        );

        if (array_key_exists('mobile', $data)) {
            $buyer = Buyer::where(['phone_number' => $data['mobile']])->first();
            // by sms
            $url = route('buyer.reset-password', ['token' => $token, 'account' => $account]);
            $buyer->notify(new ResetPasswordViaSmsNotification($url));
        } else {
            $buyer = Buyer::where(['email' => $email])->first();
            $data['name'] = $buyer->name;
            $data['token'] = $token;
            $data['account'] = $account;
            // by email
            $buyer->notify(new  ResetPasswordNotification($data));
        }


        return back()->with(['message' => 'Reset Link sent !']);
    }

    public function verify($account, PhoneNumberVerifyRequest $request)
    {
        $buyer = Auth::guard('buyer')->user();
        $otp = $buyer->OTP()->orderBy('id', 'desc')->first();
        if (!$otp) {
            return redirect()->back()->withErrors(["code" => "OTP code not found"]);
        } else {
            if ($otp->code == $request->code) {
                $buyer->is_verified = 1;
                $buyer->save();
                $otp->delete();
                if (Session::has('shop_redirect_to')) {
                    return redirect(Session::get('shop_redirect_to'));
                }
                return redirect()->route('shop.product.index', ['account' => $account]);
            }
        }
        return redirect()->back()->withErrors(["code" => "Invalid OTP code"]);
    }

    public function settingsPage()
    {
        return view('buyer::settings');
    }

    public function changePasswordPage()
    {
        return view('buyer::password.edit');
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $buyer = Auth::guard('buyer')->user();
        if (!Hash::check($data['current_password'], $buyer->getAuthPassword())) {
            return redirect()->back()->withErrors(["current_password" => "current password is not correct"]);
        }
        $buyer->update(["password" => bcrypt($data['password'])]);
        return redirect()->back()->with(['message' => 'Password has been changed']);
    }

    public function resetPasswordAction($account, ResetPasswordAfterForgetRequest $request)
    {
        $data = $request->validated();
        $token = DB::table('password_resets')->where('token', request('token'))->first();
        $expire = Carbon::parse($token?->expire);
        $isValid = $token && now()->lt($expire);
        if ($isValid) {
            $buyer = Buyer::where('email', $token->email)->first();
            if (!$buyer) {
                $buyer = Buyer::where('phone_number', $token->email)->first();
            }
            if (!$buyer) {
                abort(404);
            }
            $buyer->update(["password" => bcrypt($data['password'])]);
            return redirect()->route('shop.product.index', ['account' => $account]);
        }
        abort(403);
    }

    public function resendCode()
    {
        $buyer = auth('buyer')->user();
        //TwiloVerifyServices::sendVerificationCode($buyer->mobile);
        if(is_null($buyer)){
            return response()->json(['data' => 'error']);
        }
        $buyer->OTP()->delete();
        $otp = UserRepository::createOTP($buyer, Otp::FOR_MOBILE);
        $buyer->notify(new SendOTPViaMobileNotification($otp));

        return response()->json(['data' => 'send sms']);
    }
}

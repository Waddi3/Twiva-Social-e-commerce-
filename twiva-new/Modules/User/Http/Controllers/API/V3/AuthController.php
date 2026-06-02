<?php

namespace Modules\User\Http\Controllers\API\V3;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Business\Http\Controllers\API\V3\AuthController as BusinessAuthController;
use Modules\Business\Http\Requests\LoginRequest;
use Modules\Business\Http\Resources\BusinessResource;
use Modules\Influencer\Http\Controllers\API\V3\AuthController as InfluencerAuthController;
use Modules\Influencer\Http\Resources\InfluencerResource;
use Modules\Notification\Notifications\User\SendOTPViaEmailNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\Partner\Http\Controllers\API\V3\AuthController as PartnerAuthController;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Modules\User\Events\UserCompletedProfile;
use Modules\User\Http\Requests\Auth\forgetPasswordRequest;
use Modules\User\Http\Requests\ResetPasswordRequest;
use Modules\User\Http\Requests\sendOTPToVerifyPhoneOrEmail;
use Modules\User\Http\Requests\VerifyOTPRequest;
use Modules\User\Repository\UserRepository;
use Modules\User\Services\TwiloVerifyServices;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();
        if ($user->type == User::InfluencerType)
        {
            return InfluencerAuthController::login($data);
        }
        if ($user->type == User::BusinessType)
        {
            return BusinessAuthController::login($data);
        }
        if($user->type == User::PartnerType) {
            return PartnerAuthController::login($data);
        }
    }

    public function logout()
    {
        auth('api')->user()->token()->revoke();
        auth('api')->user()->token()->delete();
        return response()->json(['message' => 'You have been logged out successfully.']);
    }

    public function sendOTPToVerifyPhoneOrEmail(sendOTPToVerifyPhoneOrEmail $request)
    {
        $data = $request->validated();
        $user = auth('api')->user();
        if ($user->OTP()->latest()->first()?->expiry_date->isAfter(now()))
        {
            return \response()->json(['message' => "Please wait {$this->getDiffTime($user)} before requesting for a new OTP"], 403);
        }
        $user->OTP()->delete();
        if ($data['for'] == Otp::FOR_EMAIL)
        {
            $otp = UserRepository::createOTP($user, Otp::FOR_EMAIL);
            $user->notify(new SendOTPViaEmailNotification($otp));
        }
        else
        {
            /*UserRepository::createOTP($user, Otp::FOR_MOBILE, false);
            TwiloVerifyServices::sendVerificationCode($user->mobile);*/
            $otp = UserRepository::createOTP($user, Otp::FOR_MOBILE);
            $user->notify(new SendOTPViaMobileNotification($otp));

        }
        return \response()->json(['message' => 'otp has been send, please verify your account']);
    }

    public function verifyRegistrationOTP(VerifyOTPRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->firstOrFail();
        if ($user->is_verified)
        {
            return \response()->json(['message' => "This Account is verified"], 403);
        }

        $userOTP = $user->OTP()->where('otp', $data['otp'])->orWhere('otp', Otp::TWILIO_VERIFY)->orderBy('id', 'desc')->first();
        if (!$userOTP)
        {
            return response()->json(['message' => 'The selected otp is invalid'], Response::HTTP_BAD_REQUEST);
        }
        if ($userOTP->otp == Otp::TWILIO_VERIFY && !TwiloVerifyServices::verify($user->mobile, $data['otp']))
        {
            return response()->json(['message' => 'The selected otp is invalid'], Response::HTTP_BAD_REQUEST);
        }

        if (Carbon::parse(now())->isAfter($userOTP->expiry_date))
        {
            Log::debug('Code Expired!', ['now()' => now(), 'id' => $userOTP->id, 'expiry_date' => $userOTP->expiry_date, 'Carbon::parse(now())->isAfter($userOTP->expiry_date)' => Carbon::parse(now())->isAfter($userOTP->expiry_date) ? 'true' : 'false']);
            return \response()->json(['message' => 'Code Expired!'], Response::HTTP_FORBIDDEN);
        }
        UserRepository::storeFCMTokens($data['device_token'] ?? null, $user);
        $whatVerified = $userOTP->type == Otp::FOR_EMAIL ? 'email_verified_at' : 'mobile_verified_at';
        event(new UserCompletedProfile($user->id));
        $user->update(
            [
                'is_verified' => 1,
                $whatVerified => now(),
                'is_profile_completed' => 1
            ]
        );
        $user->OTP()->delete();
        $userToken = $user->createToken('myApp')->accessToken;
        if ($user->type == User::InfluencerType)
        {
            return response()->json(['data' => new InfluencerResource($user), 'token' => $userToken]);
        }
        else if ($user->type == User::BusinessType)
        {
            return response()->json(['data' => new BusinessResource($user), 'token' => $userToken]);
        }
    }

    public function verifyEmailPhoneOTP(VerifyOTPRequest $request)
    {
        $data = $request->validated();
        $user = auth('api')->user();
        $userOTP = $user->OTP()->where('otp', $data['otp'])->orWhere('otp', Otp::TWILIO_VERIFY)->orderBy('id', 'desc')->first();
        $whatVerified = $userOTP->type == Otp::FOR_EMAIL ? 'email_verified_at' : 'mobile_verified_at';
        if ($userOTP->otp == Otp::TWILIO_VERIFY && !TwiloVerifyServices::verify($user->mobile, $data['otp']))
        {
            return response()->json(['message' => 'The selected otp is invalid'], Response::HTTP_BAD_REQUEST);
        }
        if (Carbon::parse($userOTP->expiry_date)->isAfter(now()))
        {
            $user->update(
                [
                    'is_verified' => 1,
                    $whatVerified => now(),
                    'is_profile_completed' => 1
                ]
            );
            $user->OTP()->delete();

            return response()->json(['message' => ucfirst(Str::lower($userOTP->type)) . ' has been verified']);
        }
        return \response()->json(['message' => 'Code Expired!'], 403);
    }


    public function resendOtpForRegisterVerification(forgetPasswordRequest $request)
    {
        $user = User::where('email', request('email'))->first();
        if ($user->OTP()->latest()->first()?->expiry_date->isAfter(now()))
        {
            return \response()->json(['message' => "Please wait {$this->getDiffTime($user)} before requesting for a new OTP"], 403);
        }
        if ($user->is_verified)
        {
            return \response()->json(['message' => "This Account is verified"], 403);
        }
        $user->OTP()->delete();

        /*UserRepository::createOTP($user, Otp::FOR_MOBILE, false);
        TwiloVerifyServices::sendVerificationCode($user->mobile);*/
        $otp = UserRepository::createOTP($user, Otp::FOR_MOBILE);
        $user->notify(new SendOTPViaMobileNotification($otp));
        return \response()->json(['message' => 'OTP has been sent']);

    }

    public function forgetPassword(forgetPasswordRequest $request)
    {
        $user = User::where('email', request('email'))->first();
        if ($user->OTP()->latest()->first()?->expiry_date->isAfter(now()))
        {

            return \response()->json(['message' => "Please wait {$this->getDiffTime($user)} before requesting for a new OTP"], 403);
        }
        $user->OTP()->delete();

        $otp = UserRepository::createOTP($user, Otp::FOR_MOBILE);
        $otpForEmail = UserRepository::createOTP($user, Otp::FOR_EMAIL);
        $user->notify(new SendOTPViaMobileNotification($otp));
        $user->notify(new SendOTPViaEmailNotification($otpForEmail));
        return \response()->json(['message' => 'otp has been sent']);

    }

    public function verifyForgetPasswordOtp(VerifyOTPRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        $otpFromMobile = $user->OTP()->where('otp', Otp::TWILIO_VERIFY)->orderBy('id', 'desc')->first();
        $otpFromEmail = $user->OTP()->where('otp', $data['otp'])->orderBy('id', 'desc')->first();
        if ($otpFromEmail && $otpFromEmail->otp == $data['otp'] && $otpFromEmail->expiry_date->isAfter(now()))
        {
            return response()->json(['message' => 'otp is ok']);
        }
        if ($otpFromMobile && $otpFromMobile->otp == Otp::TWILIO_VERIFY && TwiloVerifyServices::verify($user->mobile, $data['otp']) && $otpFromMobile->expiry_date->isAfter(now()))
        {
            $otpFromMobile->update(['otp' => $data['otp']]);
            return response()->json(['message' => 'otp is ok']);
        }

        return response()->json(['message' => 'This Action is not allowed'], 403);

    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if (!$user->OTP()->where('otp', $data['otp'])?->first())
        {
            abort(403);
        }
        $user->update(['password' => bcrypt($data['password'])]);
        $user->OTP()->delete();
        $userToken = $user->createToken('myApp')->accessToken;
        if ($user->type == User::InfluencerType)
        {
            return response()->json(['data' => new InfluencerResource($user), 'token' => $userToken]);
        }
        else if ($user->type == User::BusinessType)
        {
            return response()->json(['data' => new BusinessResource($user), 'token' => $userToken]);
        }
    }

    public function deleteAccount()
    {

        $user = auth('api')->user();

        if (!Hash::check(request('password'), $user->getAuthPassword()))
        {
            return \response()->json(['message' => 'The password dose not match'], 403);
        }
        $user->token()->revoke();
        $user->token()->delete();
        $user->update(['fcm_token' => null]);
        $user->delete();
        return \response()->json(['message' => 'Your account has been deleted']);
    }


    private function getDiffTime(User|Authenticatable|null $user): string
    {
        $diffInMinutes = $user->OTP()->latest()->first()?->expiry_date->diffInMinutes(now());
        if (!$diffInMinutes)
        {
            $diffInSeconds = $user->OTP()->latest()->first()?->expiry_date->diffInSeconds(now());
            $time = $diffInSeconds . " seconds";
        }
        else
        {
            $time = $diffInMinutes . " minutes";
        }
        return $time;
    }

}

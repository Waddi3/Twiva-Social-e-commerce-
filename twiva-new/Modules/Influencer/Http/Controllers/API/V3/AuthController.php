<?php

namespace Modules\Influencer\Http\Controllers\API\V3;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Influencer\Http\Requests\RegisterInfluencerRequest;
use Modules\Influencer\Http\Resources\InfluencerResource;
use Modules\Influencer\Repositories\InfluencerRepository;
use Modules\Notification\Notifications\Influencer\WelcomeToTwivaNotification;
use Modules\Notification\Notifications\User\SendOTPViaEmailNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;
use Modules\User\Services\TwiloVerifyServices;

class AuthController extends Controller
{


    public function register(RegisterInfluencerRequest $request)
    {
        $data = $request->validated();
        $invitor_referral_code = $data['invitor_referral_code'] ?? null;
        if ($invitor_referral_code)
        {
            User::where('referral_code', $invitor_referral_code)->firstOrFail();
        }
        unset($data['invitor_referral_code']);

        $influencer = InfluencerRepository::createInfluencer($data);
        UserRepository::createShop($influencer);

        if ($invitor_referral_code)
        {
            UserRepository::rewardInvitor($invitor_referral_code, $influencer);
        }
        //TwiloVerifyServices::sendVerificationCode($influencer->mobile);
        $otp = UserRepository::createOTP($influencer, Otp::FOR_MOBILE);
        $influencer->notify(new SendOTPViaMobileNotification($otp));
        $influencer->notify(new WelcomeToTwivaNotification());
        registerPrivilegesToUser($influencer);
        return response()->json(['message' => "Otp sent to your {$influencer->mobile}. Please verify your account."]);
    }

    public static function login($data)
    {
        if (Auth::guard('business')->attempt(['email' => $data['email'], 'password' => $data['password']]))
        {
            $user = Auth::guard('business')->user();
            $token = $user->createToken('MyApp')->accessToken;
            if (!$user->is_verified)
            {
                return self::handleUnVerifiedUser($user);
            }
            UserRepository::storeFCMTokens($data['device_token'] ?? null, $user);
            return response()->json(['data' => new InfluencerResource($user), 'token' => $token]);
        }
        return response()->json(['message' => 'Email and password are not match'], 401);
    }

    private static function handleUnVerifiedUser(?Authenticatable $user): JsonResponse
    {
        UserRepository::createOTP($user, Otp::FOR_MOBILE);
        $otpForEmail = UserRepository::createOTP($user, Otp::FOR_EMAIL);
        $via = 'Mobile';
        if ($user->phone_number)
        {
            try
            {
                //TwiloVerifyServices::sendVerificationCode($user->mobile);
                $otp = UserRepository::createOTP($user, Otp::FOR_MOBILE);
                $user->notify(new SendOTPViaMobileNotification($otp));

            }
            catch (Exception)
            {
                $via = 'Email';
                $user->notify(new SendOTPViaEmailNotification($otpForEmail));
            }
        }
        else
        {
            $via = 'Email';
            $user->notify(new SendOTPViaEmailNotification($otpForEmail));
        }
        return response()->json(['data' => [
            'status' => true,
            'is_verified' => 0,
            'email' => $user->email,
            'message' => "Otp sent to your $via. Please verify your account.",
            'is_business' => $user->type == 'Business'
        ]], 403);
    }

}





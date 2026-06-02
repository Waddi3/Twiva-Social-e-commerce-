<?php

namespace Modules\Business\Http\Controllers\API\V3;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Business\Http\Requests\RegisterBusinessRequest;
use Modules\Business\Http\Resources\BusinessResource;
use Modules\Business\Repository\BusinessRepository;
use Modules\Notification\Notifications\Influencer\WelcomeToTwivaNotification;
use Modules\Notification\Notifications\User\SendOTPViaEmailNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;
use Modules\User\Services\TwiloVerifyServices;

class AuthController extends Controller
{

    public function register(RegisterBusinessRequest $request)
    {
        $data = $request->validated();
        $invitor_referral_code = $data['invitor_referral_code'] ?? null;

        if ($invitor_referral_code)
        {
            User::where('referral_code', $invitor_referral_code)->firstOrFail();
        }
        unset($data['invitor_referral_code']);
        $business = BusinessRepository::createBusiness($data);
        UserRepository::createShop($business);
        if ($invitor_referral_code)
        {
            UserRepository::rewardInvitor($invitor_referral_code, $business);
        }

        $otp = UserRepository::createOTP($business, Otp::FOR_MOBILE);
        $business->notify(new SendOTPViaMobileNotification($otp));
        $business->notify(new WelcomeToTwivaNotification());
        registerPrivilegesToUser($business);
        return response()->json(['message' => "OTP sent to your {$business->mobile}. Please verify your account."]);
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
            return response()->json(['data' => new BusinessResource($user), 'token' => $token]);
        }
        return response()->json(['message' => 'Email and password are not match'], 401);

    }

    private static function handleUnVerifiedUser(User $user): JsonResponse
    {
        $user->OTP()->delete();
        $otpForEmail = UserRepository::createOTP($user, Otp::FOR_EMAIL);
        $via = 'Mobile';
        if ($user->phone_number)
        {
            try
            {
                $otp = UserRepository::createOTP($user, Otp::FOR_MOBILE);
                //TwiloVerifyServices::sendVerificationCode($user->mobile);
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
            'message' => "OTP sent to your $via. Please verify your account.",
            'is_business' => $user->type == User::BusinessType
        ]], 403);
    }

}





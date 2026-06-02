<?php

namespace Modules\Buyer\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Entities\BuyerOTP;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerRegisterRequest;
use Modules\Notification\Notifications\Buyer\VerifyPhoneNumberNotification;
use Modules\Notification\Notifications\User\SendOTPViaMobileNotification;
use Modules\User\Entities\Otp;
use Modules\User\Repository\UserRepository;
use Modules\User\Services\TwiloVerifyServices;
use NotificationChannels\Twilio\TwilioChannel;
use Symfony\Component\HttpFoundation\Response;

class ApiCustomerRegisterController
{
    public function register(ApiCustomerRegisterRequest $request)
    {
        $user = auth('api')->user();

        $validated = $request->validated();
        $customer = Buyer::where('phone_number', $validated['phone_number'])
//            ->whereNull('verified_at')
            ->where('is_verified', 0)
            ->first();
        $validated['created_by'] = $user->id;
        if ($customer)
        {
            $customer->update($validated);
        }

//        $name = "{$validated['first_name']} {$validated['last_name']}";
        $name = "{$validated['name']}";
        try
        {
            [$first_name, $last_name] = Str::of($name)->explode(' ', 2);
        } catch (Exception $exception)
        {
            $last_name = ' ';
        }
        [$phone_number, $phone_code, $mobile] = Buyer::splitPhoneNumber($validated['phone_number'], $validated['phone_code']);
        $customer_data = [
            'name' => $name,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone_code' => $phone_code,
            'phone_number' => $phone_number,
            'class' => Buyer::INDIVIDUAL,
            'rep_name' => $name,
            'rep_phone_code' => $phone_code,
            'rep_phone_number' => $phone_number,
            'created_by' => $validated['created_by'],
            'is_verified' => 0,
        ];
        $customer = Buyer::create($customer_data);
        $address_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'county_id' => $validated['county_id'] ?? null,
            'phone_number' => $mobile ?? null,
            'email' => $validated['email'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'created_by' => $validated['created_by'] ?? null,
        ];
        if ($validated['longitude'] && $validated['latitude'] && $validated['country_id'] && $validated['delivery_address'])
        {
            $address = $customer->addresses()->create($address_data);
        }
        return response()->json(['data' => ['customer' => $customer->makeHidden('password'), 'address' => $address ?? null]]);
    }


    public function sendOtp()
    {
//        $phone_number = request('phone_number');
        $buyer_id = request('buyer_id');
        $user = auth('api')->user();
        $customer = Buyer::where('id', $buyer_id)
            ->where('created_by', $user->id)
//            ->whereNull('verified_at')
            ->where('is_verified', 0)
            ->first();
        if ($customer)
        {
            $otp = $customer->otp;
            if ($otp)
            {
                if ($otp->attempts < 15)
                {
                    $otp->update([
                        'attempts' => $otp->increment('attempts'),
                    ]);
                }
                else
                {
                    return response()->json(['data' => ['msg' => 'Please try in another time']], 403);
                }
            }
            else
            {
                $customer->OTP()->create([
                    'code' => BuyerOTP::generateOtpCode(),
                ]);
            }
            //TwiloVerifyServices::sendVerificationCode($customer->mobile);
            $otp = UserRepository::createOTP($customer, Otp::FOR_MOBILE);
            $customer->notify(new SendOTPViaMobileNotification($otp));
            return response()->json(['data' => ['msg' => 'OTP code send to Customer']]);
        }
        return response()->json(['data' => ['msg' => 'Please check your data']], 403);
    }

    public function checkOtp()
    {
        $buyer_id = request('buyer_id');
        $user = auth('api')->user();
        $code = request('code');
        $customer = Buyer::where('id', $buyer_id)
            ->where('created_by', $user->id)
//            ->whereNull('verified_at')
            ->where('is_verified', 0)
            ->first();
        if ($customer)
        {
            if (TwiloVerifyServices::verify($customer->mobile, $code))
            {
                $customer->update([
                    'is_verified' => 1
                ]);
                $customer->OTP()->delete();
                return response()->json(['data' => ['msg' => 'Customer is verified now']]);
            }
        }
        return response()->json(['data' => ['msg' => 'Please check your data']], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

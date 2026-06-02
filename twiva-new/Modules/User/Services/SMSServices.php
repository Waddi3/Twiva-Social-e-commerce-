<?php

namespace Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class SMSServices
{
    public static function notify($user, $message)
    {
        self::build(self::userPhone($user), $message);
    }

    public static function smsOtp($user, $otp)
    {
        if (app()->environment() != 'local')
        {
            self::build(self::userPhone($user), 'Twiva OTP code is : ' . $otp);
        }
    }

    public static function userPhone($user)
    {
        if (gettype($user) == 'string')
        {
            return $user;
        }
        return (Str::contains($user->phone_code, '+')
                ? $user->phone_code
                : '+' . $user->phone_code
            ) . $user->phone_number;
    }


    private static function build($phone, $message)
    {
        $twilio = new Client(config('services.Twilio.sid'), config('services.Twilio.token'));
        try
        {
            $message = $twilio->messages->create($phone, ["body" => $message, "from" => config('services.Twilio.from')]);
        }
        catch (Exception $exception)
        {
            Log::debug('SMS Services 36', ['phone' => $phone, '$exception' => $exception]);
        }
    }

}

<?php


namespace Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwiloVerifyServices
{
    public static function sendVerificationCode($mobile): bool
    {
        try
        {
            $twilio = new Client(config('services.Twilio.sid'), config("services.Twilio.token"));
            $twilio->verify->v2->services(config('services.Twilio.service_sid'))
                ->verifications->create($mobile, "sms");
            return true;
        }
        catch (Exception $e)
        {
            Log::debug('error when send twilio verification', ['message' => $e]);
            return false;
        }
    }

    public static function verify($mobile, $code): bool
    {
        try
        {
            $twilio = new Client(config('services.Twilio.sid'), config("services.Twilio.token"));
            $verification_check = $twilio->verify->v2->services(config('services.Twilio.service_sid'))
                ->verificationChecks->create([
                        "to" => $mobile,
                        "code" => $code
                    ]
                );
            return $verification_check->status == "approved";
        }
        catch (TwilioException $e)
        {
            return false;
        }

    }

}
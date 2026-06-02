<?php


namespace Modules\Payment\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Home\Entities\Transaction;

class MPESAService
{
    public static function createTransaction($amount, $phone)
    {
        return self::customerMpesaSTKPushBuyer($amount, $phone);
    }

    private static function customerMpesaSTKPushBuyer($amount, $phone)
    {

        $url = config('services.MPESA.MPESA_STKPUSH_URL');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer ' . self::generateAccessToken()]);

        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => config('services.MPESA.MPESA_SHORT_CODE'),
            'Password' => self::lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone, //254728851199, // replace this with your phone number
            'PartyB' => config('services.MPESA.MPESA_SHORT_CODE'),
            'PhoneNumber' => $phone, // replace this with your phone number
            'CallBackURL' => route('mpesa.callback'),
            'AccountReference' => "Twiva",
            'TransactionDesc' => "Twiva M-Pesa"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        return $curl_response;
    }

    public static function lipaNaMpesaPassword()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = config('services.MPESA.MPESA_PASS_KEY');
        $BusinessShortCode = config('services.MPESA.MPESA_SHORT_CODE');
        $timestamp = $lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode . $passkey . $timestamp);
        return $lipa_na_mpesa_password;
    }

    private static function generateAccessToken($MPESA_CONSUMER_KEY = 'MPESA_CONSUMER_KEY', $MPESA_CONSUMER_SECRET = 'MPESA_CONSUMER_SECRET')
    {
        try {

            $client = new Client();
            $request = $client->request(
                'GET',
                config('services.MPESA.MPESA_O_AUTH_URL'),
                ['auth' => [
                    config("services.MPESA.$MPESA_CONSUMER_KEY"),
                    config("services.MPESA.$MPESA_CONSUMER_SECRET")
                ]]
            );
            return json_decode($request->getBody()->getContents())->access_token;
        } catch (Exception $exception) {
            logger('can not generateAccessToken for mpesa =>', ['exception' => $exception]);
        }
    }

    public static function hasError($transactionResponse)
    {
        $transactionResponse = json_decode($transactionResponse);
        Log::debug('$transactionResponse', ['data' => $transactionResponse]);

        if (isset($transactionResponse->errorMessage)) {
            return ['status' => false, 'type' => 'transaction', 'message' => $transactionResponse->errorMessage];
        }
    }


    public static function responseToFailedPayment($transactionID, $responseCode)
    {
        $transaction = Transaction::where('transaction_id', $transactionID)->firstOrFail();
        if ($responseCode == 1037) {
            if (app()->environment() == 'production') {
                $transaction->update(['status' => Transaction::RequestTimeout, 'transaction_details' => 'RequestTimeout']);
            }
        } elseif ($responseCode == 1032) {
            $transaction->update(['status' => Transaction::RequestCancelledByUser, 'transaction_details' => 'RequestCancelledByUser']);
        } elseif ($responseCode == 1) {
            $transaction->update(['status' => Transaction::TheBalanceIsInsufficientForTheTransaction, 'transaction_details' => 'TheBalanceIsInsufficientForTheTransaction']);
        } else {
            $transaction->update(['status' => Transaction::CaseNotDetected, 'transaction_details' => 'CaseNotDetected']);
        }
    }

    /**
     * @param $phone_number '254712123456'
     * @param $amount
     * @param string $command_id || SalaryPayment, BusinessPayment or PromotionPayment
     * @param string $remarks || (Sentence of up to 100 characters.) => pay for influencer for 12-09-2023 amount 120 KSH
     * @param string $occasion || (Sequence of characters up to 100)
     * @return
     */
    public static function BusinessToCustomer($phone_number, $amount, string $command_id = 'BusinessPayment', string $remarks = "", string $occasion = "null")
    {
        $url = config('services.MPESA.MPESA_BASE_URL') . '/mpesa/b2c/v3/paymentrequest';
        $body_request = [
            "OriginatorConversationID" => Str::ulid()->toRfc4122(),
            "InitiatorName" => config('services.MPESA.MPESA_INITIATOR_NAME'),
            "SecurityCredential" => self::generate_security_credential(config('services.MPESA.MPESA_INITIATOR_PASSWORD')),
            //            "InitiatorName" => config('services.MPESA.MPESA_SHORT_CODE'),
            //            "SecurityCredential" => config('services.MPESA.MPESA_YOUR_SECURITY_CREDENTIAL'),
            "CommandID" => $command_id,
            "Amount" => round($amount),
            "PartyA" => config('services.MPESA.MPESA_B2C_SHORT_CODE'),
            //            "PartyA" => '600987',
            "PartyB" => self::RemovePlusFromPhoneNumber($phone_number),
            "Remarks" => $remarks,
            "QueueTimeOutURL" => route('mpesa.b2c.queue.callback'), // 'https://api.dev.twiva.co.ke/v1/payment/mpesa/b2c/queue',
            "ResultURL" => route('mpesa.b2c.result.callback'), // 'https://api.dev.twiva.co.ke/v1/payment/mpesa/b2c/result',
            "Occasion" => $occasion,
            //            "Occassion" => $occasion
        ];

        //        dd((object)$body_request, self::generateAccessToken());
        //        logger("Modules/Payment/Services/MPESAService.php:126 => ", ['body_request' => $body_request]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::generateAccessToken('MPESA_B2C_CONSUMER_KEY', 'MPESA_B2C_CONSUMER_SECRET')
        ])
            ->withBody(json_encode($body_request), 'application/json')
            ->post($url);
        Log::error('MPESA Service BusinessToCustomer 145',['$response' =>$response]);
        return $response;
    }

    public static function BusinessPayBill($till_number, $amount, string $command_id = 'BusinessPayToBulk', string $remarks = "", string $occasion = "null")
    {
        $url = config('services.MPESA.MPESA_BASE_URL') . '/mpesa/b2b/v1/paymentrequest';
        $body_request = [
            "OriginatorConversationID" => Str::ulid()->toRfc4122(),
            // "InitiatorName" => config('services.MPESA.MPESA_INITIATOR_NAME'),
            "Initiator" => config('services.MPESA.MPESA_INITIATOR_NAME'),
            "SecurityCredential" => self::generate_security_credential(config('services.MPESA.MPESA_INITIATOR_PASSWORD')),
            "CommandID" => $command_id,
            "Amount" => round($amount),
            "PartyA" => config('services.MPESA.MPESA_B2C_SHORT_CODE'),
            "PartyB" => $till_number,
            "AccountReference" => "$till_number",
            "Remarks" => $remarks,
            "QueueTimeOutURL" => route('mpesa.b2c.queue.callback'), // 'https://api.dev.twiva.co.ke/v1/payment/mpesa/b2c/queue',
            "ResultURL" => route('mpesa.b2c.result.callback'), // 'https://api.dev.twiva.co.ke/v1/payment/mpesa/b2c/result',
            // PayBill
            "SenderIdentifierType" => "4",
            "RecieverIdentifierType" => "4",
        ];

        //        dd((object)$body_request, self::generateAccessToken());
        //        logger("Modules/Payment/Services/MPESAService.php:126 => ", ['body_request' => $body_request]);
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::generateAccessToken('MPESA_B2C_CONSUMER_KEY', 'MPESA_B2C_CONSUMER_SECRET')
        ])
            ->withBody(json_encode($body_request), 'application/json')
            ->post($url);
        logger('BusinessPayBill');
        Log::error('MPESA Service BusinessPayBill 179',['$response' =>$response]);
        return $response;
    }

    private static function RemovePlusFromPhoneNumber($phone_number)
    {
        return str_starts_with($phone_number, '+') ? str_replace('+', '', $phone_number) : $phone_number;
    }

    public static function generate_security_credential($text)
    {
        if (config('services.MPESA.MPESA_ENVIRONMENT') == 'sandbox') {
            $pub_key = File::get(module_path('Payment', 'certificates/SandboxCertificate.cer'));
        } else {
            $pub_key = File::get(module_path('Payment', 'certificates/ProductionCertificate.cer'));
        }
        $pub_key = openssl_get_publickey($pub_key);
        openssl_public_encrypt($text, $output, $pub_key, OPENSSL_PKCS1_PADDING);

        return base64_encode($output);
    }

    public static function checkTransactionStatus($transaction, $MPESA_TRANSACTION_MINI_APP_CONSUMER_KEY = 'MPESA_CONSUMER_KEY', $MPESA_TRANSACTION_MINI_APP_CONSUMER_SECRET = 'MPESA_CONSUMER_SECRET')
    {
        $url = config('services.MPESA.MPESA_BASE_URL') . '/mpesa/transactionstatus/v1/query';
        $body_request = [
            "Initiator" => config('services.MPESA.MPESA_INITIATOR_NAME_TRANSACTION_STATUS'), // "testapiuser",
            "SecurityCredential" => self::generate_security_credential(config('services.MPESA.MPESA_INITIATOR_PASSWORD_TRANSACTION_STATUS')),
            "CommandID" => "TransactionStatusQuery",
            "TransactionID" => $transaction->transaction_id, // "NEF61H8J60",
            "OriginatorConversationID" => $transaction->transaction_id, // Str::ulid()->toRfc4122(), // "AG_20190826_0000777ab7d848b9e721",
            "PartyA" => config('services.MPESA.MPESA_TILL_NUMBER'), // "600782",
            "IdentifierType" => "4", // 1=> MSISDN | 2 => till number | 4 => organization short code
            "ResultURL" => route('mpesa.check-transaction-status.result.callback'), // "https://api.dev.twiva.co.ke/v1/payment/mpesa/check-transaction-status/result",
            "QueueTimeOutURL" => route('mpesa.check-transaction-status.queue.callback'), // "https://api.dev.twiva.co.ke/v1/payment/mpesa/check-transaction-status/queue",
            "Remarks" => "OK",
            "Occasion" => "OK",
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::generateAccessToken($MPESA_TRANSACTION_MINI_APP_CONSUMER_KEY, $MPESA_TRANSACTION_MINI_APP_CONSUMER_SECRET)
        ])
            ->withBody(json_encode($body_request), 'application/json')
            ->post($url);
        return $response;
    }
}

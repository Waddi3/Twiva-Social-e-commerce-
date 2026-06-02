<?php


namespace Modules\Payment\Services;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\ShippingAddress;
use Modules\User\Entities\User;
use function Symfony\Component\Translation\t;

class FlutterWaveService
{
    public static function createTransactionForShopSite($data)
    {
        $address = ShippingAddress::find(Session::get('address_id'));
        $generateMerchantRequestID = Transaction::generateMerchantRequestID();
        $amount  = $data['transaction_amount'] +  round($data['transaction_amount'] * Transaction::FlutterwaveTransactionFees);
        $transactionData = [
            "tx_ref" => $generateMerchantRequestID,
            "amount" => $amount,
            "currency" => 'KES',
            "redirect_url" => route('flutterwave.callback'),
            'customer' => [
                'name' => $address->first_name . ' ' . $address->last_name,
                'email' => $address->email ?? "info@twiva.co.ke",
                'phonenumber' => $address->phone_number,
            ],
            'customizations' => [
                'title' => "Twiva checkout",
                'logo' => asset('front/landing/images/icons/Union.svg')
            ],

        ];
        try {
            $secretKey = config('services.Flutterwave.secret_key');
            $client = new Client();
            $result = $client->post("https://api.flutterwave.com/v3/payments",
                [
                    'headers' => [
                        "Authorization" => "Bearer $secretKey",  //Secret key of your account
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($transactionData),
                ]);
            $result = json_decode($result->getBody()->getContents());
            if (isset($result->status) && $result->status == 'success') {
                if (isset($result->data->link) && $result->data->link != ' ') {
                    $redirectLink = $result->data->link;
                    return [$generateMerchantRequestID, $redirectLink];
                }
            }
        } catch (GuzzleException $e) {
            Log::debug('error when creating flutter-wave transaction');
            throw new Exception($e);
        }

    }

    public static function createTransactionForCampaignPayments(Transaction $transaction, User $user)
    {
        $generateMerchantRequestID = $transaction->transaction_id ?? Transaction::generateMerchantRequestID();
        $transactionData = [
            "tx_ref" => $generateMerchantRequestID,
            "amount" => $transaction->transaction_amount,
            "currency" => 'KES',
            "redirect_url" => route('flutterwave.callback'),
            'customer' => [
                'name' => $user->contact_name ?? $user->name,
                'email' => $user->email ?? "info@twiva.co.ke",
                'phonenumber' => $user->phone_number,
            ],
            'customizations' => [
                'title' => "Twiva checkout",
                'logo' => asset('front/landing/images/icons/Union.svg')
            ],
        ];
        try {
            $secretKey = config('services.Flutterwave.secret_key');
            $client = new Client();
            $result = $client->post("https://api.flutterwave.com/v3/payments",
                [
                    'headers' => [
                        "Authorization" => "Bearer $secretKey",  //Secret key of your account
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($transactionData),
                ]);
            $result = json_decode($result->getBody()->getContents());
            if (isset($result->status) && $result->status == 'success') {
                if (isset($result->data->link) && $result->data->link != ' ') {
                    $redirectLink = $result->data->link;
                    return [$generateMerchantRequestID, $redirectLink];
                }
            }
        } catch (GuzzleException $e) {
            Log::debug('error when creating flutter-wave transaction');
            throw new Exception($e);
        }

    }

    public static function verfiyTransaction($transaction, $transaction_id)
    {
        try {
            $secretKey = config('services.Flutterwave.secret_key');
            $client = new Client();
            $result = $client->request('GET', "https://api.flutterwave.com/v3/transactions/$transaction_id/verify",
                [
                    'headers' => [
                        "Authorization" => "Bearer $secretKey",  //Secret key of your account
                        'Content-Type' => 'application/json',
                    ],
                ]);
            $finalResult = json_decode($result->getBody()->getContents());
            Log::debug('flutterwave verify Transaction',
                [
                    'finalResult' => $finalResult,
                    'transaction_id' => $transaction_id,
                    'transaction' => $transaction
                ]);
            return $finalResult?->status == 'success';
//            return ((int)$finalResult?->data?->amount == (int)$transaction->transaction_amount) && ($finalResult?->status == 'success');
        } catch (Exception $e) {
            Log::debug('error when verifying Flutterwave translation', ['error' => $e, '$transaction' => $transaction, '$transaction_id' => $transaction]);
            return false;
        }

    }


}



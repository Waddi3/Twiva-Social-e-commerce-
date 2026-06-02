<?php

namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\Home\Entities\Transaction;
use Modules\User\Entities\User;

class PesapalService
{
    protected $config;
    protected $client;
    protected $host;
    protected $token_response;
    protected $token;

    public function __construct()
    {
        $this->config = config('twiva.pesapal');
        $this->client = new \GuzzleHttp\Client();
        // $this->host = $this->config['HOST'];
        $this->fetchToken();
    }

    public function getHost($uri)
    {
        if ($this->config['sandbox']) {
            return $this->config['sandbox_host'] . $this->config['sandbox_uri'] . $uri;
        }
        return $this->config['live_host'] . $this->config['live_uri'] . $uri;
    }

    public function fetchToken()
    {
        try {
            $response = $this->client->post($this->getHost('Auth/RequestToken'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    "consumer_key" => $this->config['ConsumerKey'],
                    "consumer_secret" => $this->config['ConsumerSecret'],
                ],
            ]);
            $this->token_response = json_decode($response->getBody()->getContents());
            $this->token = $this->token_response->token;
        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            echo "Request Error: " . $exception->getMessage();
            if ($exception->hasResponse()) {
                echo "\nResponse: " . $exception->getResponse()->getBody()->getContents();
            }
        }
    }

    public function  getNotificationId()
    {
        $notification_id = null;
        if ($this->token) {
            $IPNs_list_request = $this->client->get($this->getHost('URLSetup/GetIpnList'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
            $IPNs_list_response = json_decode($IPNs_list_request->getBody()->getContents());
            if (count($IPNs_list_response)) {
                $notification_id = head($IPNs_list_response)['ipn_id'];
            } else {
                $notification_id = $this->IPNUrlRegistration();
            }
        }
        return $notification_id;
    }

    public function IPNUrlRegistration()
    {
        $IPNs_list_request = $this->client->post($this->getHost('URLSetup/RegisterIPN'), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'json' => [
                "url" => route('pesapal-callback-url'),
                "ipn_notification_type" => "GET"
            ]
        ]);
        $IPNs_list_response = json_decode($IPNs_list_request->getBody()->getContents());
        return $IPNs_list_response->ipn_id ?? null;
    }

    public function createRequestPayment($orders, $transaction_details, $data, $transaction_type = "SpotitCustomerPayOnline")
    {
        try {
            if ($this->token) {
                $json_body = [
                    "id" =>  Str::uuid()->toString(),
                    "currency" =>  "KES",
                    "amount" =>  $transaction_details['transaction_amount'],
                    "description" =>  "Pay for orders: " . $orders->pluck('id')->implode(', '),
                    "callback_url" =>  route('pesapal-callback-url'),
                    "redirect_mode" =>  "",
                    "notification_id" =>  $this->getNotificationId(),
                    "branch" =>  "TWIVA",
                    "billing_address" =>  [
                        "email_address" =>  "",
                        "phone_number" =>  $this->preparePhoneNumber($transaction_details['phone_number']),
                        "country_code" =>  "KE",
                        "first_name" =>  "",
                        "middle_name" =>  "",
                        "last_name" =>  "",
                        "line_1" =>  "Pesapal Limited",
                        "line_2" =>  "",
                        "city" =>  "",
                        "state" =>  "",
                        "postal_code" =>  "",
                        "zip_code" =>  ""
                    ]
                ];

                $create_request_payment = $this->client->post($this->getHost('Transactions/SubmitOrderRequest'), [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token,
                    ],
                    'json' => $json_body
                ]);
                $response = json_decode($create_request_payment->getBody()->getContents());
                return $response;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Get the response body from the exception
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                return $response;
            }
            return null;
        } catch (\Exception $e) {
            // Log the error or handle it accordingly
            logger('Spotit Payment request failed: ' . $e->getMessage(), ['body' => json_decode($body)]);
            return null; // or handle the error as needed
        }
        return $response ?? null;
    }

    public function preparePhoneNumber($phone_number)
    {
        return Str::startsWith($phone_number, "0") ? $phone_number : "0$phone_number";
    }

    public function pay($transaction_details, $orders, $data, $confirm_url)
    {
        $response = self::createRequestPayment($orders, $transaction_details, $data);
        $transaction_details['payment_gateway_type'] = Transaction::PesapalGateAway;
        $redirect_url = "";
        if ($response) {
            if ($response->status == 200) {
                $transaction_details['transaction_id'] = $response->order_tracking_id ?? null;
                $transaction_details['originator_conversation_id'] = $response->merchant_reference ?? null;
                Session::put('transaction_id', $response->order_tracking_id ?? null);
                Session::put('confirm_url', $confirm_url);
                $transaction_details['confirm_url'] = $confirm_url;
                $redirect_url = $response->redirect_url ?? '';
            } else {
                $transaction_details['status'] = Transaction::TransactionFailed;
            }
            $transaction_details['request_response'] = $response;
        } else {
            $transaction_details['status'] = Transaction::TransactionFailed;
        }
        return [$transaction_details, $redirect_url];
    }

    public function getTransactionStatus($order_tracking_id)
    {
        $transaction_status_response = null;
        if ($this->token) {
            $transaction_status_request = $this->client->get($this->getHost("Transactions/GetTransactionStatus?orderTrackingId={$order_tracking_id}"), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
            $transaction_status_response = json_decode($transaction_status_request->getBody()->getContents(), true);
        }
        return $transaction_status_response;
    }
}

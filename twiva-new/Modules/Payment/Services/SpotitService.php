<?php

namespace Modules\Payment\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Modules\Home\Entities\Transaction;
use Modules\User\Entities\User;

class SpotitService
{
    protected $spotit_config;
    protected $client;
    protected $host;
    protected $auth_info;
    protected $token_response;
    protected $token;

    public function __construct()
    {
        $this->spotit_config = config('twiva.spotit');
        $this->client = new Client();
        $this->host = $this->spotit_config['SPOTIT_HOST'];
        $this->auth_info = $this->prepareAuthorizationInfo();
        $this->fetchToken();
    }

    public function prepareAuthorizationInfo()
    {
        return $this->spotit_config['ConsumerKey'] . ":" . $this->spotit_config['ConsumerSecret'];
    }

    public function fetchToken()
    {
        $response = $this->client->get($this->host . '/v1/authenticate', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->auth_info),
            ],
        ]);
        $this->token_response = json_decode($response->getBody()->getContents());
        $this->token = $this->token_response->Token;
    }

    public function  fetchListOfBanks()
    {
        $banks = [];
        if ($this->token) {
            $list_bank_request = $this->client->get($this->host . '/v1/banks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);
            $list_bank_response = json_decode($list_bank_request->getBody()->getContents());
            $banks = $list_bank_response->Banks;
        }
        return $banks;
    }

    public function createRequestPayment($orders, $phone_number, $data, $transaction_type = "SpotitCustomerPayOnline")
    {
        try {
            if ($this->token) {
                $timestamp = now()->format("YmdHis");
                $BusinessShortCode = $this->spotit_config['BusinessShortCode'];
                $PassKey = $this->spotit_config['PassKey'];
                $body = json_encode([
                    "BusinessShortCode" => $BusinessShortCode,
                    "Password" => base64_encode("$BusinessShortCode$PassKey$timestamp"),
                    "Timestamp" => $timestamp,
                    "TransactionType" => $transaction_type,
                    "Cart" => $this->prepareCart($orders),
                    "PartyA" => $this->preparePhoneNumber($phone_number),
                    "PartyB" => $BusinessShortCode,
                    "PhoneNumber" => $this->preparePhoneNumber($phone_number),
                    "Bank" => $this->prepareBankData($data),
                    "CallbackURL" => route('spotit-callback-url'),
                    "AccountReference" => Str::uuid()->toString(),
                    "TransactionDesc" => "Pay for orders: " . $orders->pluck('id')->implode(', '),

                ]);

                $create_request_payment = $this->client->post($this->host . '/v1/spotitcode', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token,
                    ],
                    'body' => $body
                ]);
                $response = json_decode($create_request_payment->getBody()->getContents());
                return $response;
            }
        } catch (ClientException $e) {
            // Get the response body from the exception
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                return $response;
            }
            return null;
        } catch (Exception $e) {
            // Log the error or handle it accordingly
            logger('Spotit Payment request failed: ' . $e->getMessage(), ['body' => json_decode($body)]);
            return null; // or handle the error as needed
        }
        return $response ?? null;
    }

    public function preparePhoneNumber($phone_number)
    {
        return Str::startsWith($phone_number, "254") ? $phone_number : "254$phone_number";
    }
    public function prepareBankData($data)
    {
        $bank = explode(":", $data['spotit_bank']);
        return (object)[
            "BankId" => $bank[0] ?? '',
            "BankName" => $bank[1] ?? '',
        ];
    }

    public function prepareCart($orders)
    {
        $cart = [];
        foreach ($orders as $order) {
            $user_type = $order->shop->user()->withTrashed()->first()->type;
            foreach ($order->orderDetails as $order_detail) {
                $cart[] = (object)[
                    "SKUCode" => $order_detail->product_id,
                    "Name" => $user_type == User::InfluencerType
                        ? $order_detail->productFromInfluencerShop->productDetails->name
                        : $order_detail->businessProductDetails->name,
                    "Description" => $user_type == User::InfluencerType
                        ? $order_detail->productFromInfluencerShop->productDetails->description
                        : $order_detail->businessProductDetails->description,
                    "Quantity" => $order_detail->quantity,
                    "Price" => $order_detail->amount * $order_detail->quantity,
                    "Action" => $this->spotit_config['Action'],
                ];
            }
        }
        return $cart;
    }

    public function payWithSpotit($transaction_details, $orders, $data)
    {
        $response = self::createRequestPayment($orders, $transaction_details['phone_number'], $data);
        $transaction_details['payment_gateway_type'] = Transaction::SpotitGateAway;
        if ($response) {
            if ($response->ResponseCode == 200) {
                $transaction_details['transaction_id'] = $response->SpotitCheckoutCode ?? null;
                // $transaction_details['originator_conversation_id'] = $response->MerchantCode ?? null;
                Session::put('transaction_id', $response->SpotitCheckoutCode ?? null);
            } else {
                $transaction_details['status'] = Transaction::TransactionFailed;
            }
            $transaction_details['request_response'] = $response;
        } else {
            $transaction_details['status'] = Transaction::TransactionFailed;
        }
        return $transaction_details;
    }
}

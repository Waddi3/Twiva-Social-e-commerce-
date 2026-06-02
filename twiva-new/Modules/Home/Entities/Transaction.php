<?php

namespace Modules\Home\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;

class Transaction extends Model {


    protected $table = 'transactions';
    const FlutterwaveTransactionFees = .029;
    #request type
    const SUBSCRIPTION_PURCHASE = 1;
    const BUYER_PURCHASE = 2;
    const BUSINESS_PAY_FOR_CAMPAIGN = 3;
    const BUYER_PURCHASE_GROUP_PAY = 4;
    const TRANSACTION_TYPES = [
        Transaction::SUBSCRIPTION_PURCHASE     => 'Subscription',
        Transaction::BUYER_PURCHASE            => 'Order',
        Transaction::BUSINESS_PAY_FOR_CAMPAIGN => 'Campaign'
    ];
    # payment gateway
    const MPESAGateAway = 1;
    const FlutterWaveGateAway = 2;
    const LipaLetterGateAway = 3;
    const PaypalGateAway = 4;
    const PayOnDeliveryGateAway = 5;
    const SpotitGateAway = 6;
    const PesapalGateAway = 8;
    const Gateways = [
        Transaction::MPESAGateAway         => 'MPESA',
        Transaction::FlutterWaveGateAway   => 'FlutterWave',
        Transaction::LipaLetterGateAway    => 'LipaLetter',
        Transaction::PaypalGateAway        => 'Paypal',
        Transaction::PayOnDeliveryGateAway => 'PayOnDelivery',
        Transaction::SpotitGateAway        => 'Spotit',
        Transaction::PesapalGateAway => 'Pesapal',
    ];

    # transaction status
    const TransactionFailed = -1;
    const WaitingForPayment = 0;
    const PaymentSuccessfully = 1;
    const RequestCancelledByUser = 2;
    const RequestTimeout = 3;
    const TheBalanceIsInsufficientForTheTransaction = 4;
    const CaseNotDetected = 5;
    const TransactionStatuses = [
        Transaction::TransactionFailed                         => 'Failed',
        Transaction::WaitingForPayment                         => 'Waiting For Payment',
        Transaction::PaymentSuccessfully                       => 'Payment Successfully',
        Transaction::RequestCancelledByUser                    => 'Request CancelledBy User',
        Transaction::RequestTimeout                            => 'Request Timeout',
        Transaction::TheBalanceIsInsufficientForTheTransaction => 'The Balance Is Insufficient',
        Transaction::CaseNotDetected                           => 'Case Not Detected',
        null                                                   => 'Pay On Delivery'
    ];
    const TRANSACTION_STATUSES_CLASSES = [
        Transaction::TransactionFailed                         => 'eshop-failed',
        Transaction::WaitingForPayment                         => 'in-process',
        Transaction::PaymentSuccessfully                       => 'eshop-active',
        Transaction::RequestCancelledByUser                    => 'eshop-failed',
        Transaction::RequestTimeout                            => 'eshop-failed',
        Transaction::TheBalanceIsInsufficientForTheTransaction => 'eshop-failed',
        Transaction::CaseNotDetected                           => 'in-process',
        null                                                   => 'eshop-upcoming'
    ];

    protected $guarded = [];

    public static function generateMerchantRequestID($gateway = 'Flutterwave', $merchantRequestID = null)
    {
        $existTransactionIDS = Transaction::select('transaction_id')->get()->pluck('transaction_id')->toArray();
        $id = $gateway . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(1);
        while (in_array($id, $existTransactionIDS))
        {
            $id = $gateway . '-' . Str::random(5) . '-' . Str::random(5) . '-' . Str::random(1);
        }
        return $id;
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_transactions');
    }

    public function user()
    {
        if ($this->request_type == self::SUBSCRIPTION_PURCHASE || $this->request_type == self::BUSINESS_PAY_FOR_CAMPAIGN)
        {
            return $this->belongsTo(User::class, 'user_id');
        }
        return $this->belongsTo(Buyer::class, 'user_id');
    }

    public function getUserDataAttribute()
    {
        if ($this->user)
        {
            return $this->user->name;
        }
        return $this->phone_number;
    }

    public function getGatewayNameAttribute()
    {
        return match ($this->payment_gateway_type)
        {
            self::MPESAGateAway => "MPESA",
            self::FlutterWaveGateAway => "FlutterWave",
            self::LipaLetterGateAway => "LipaLetter",
            self::PaypalGateAway => "Paypal",
            self::PayOnDeliveryGateAway => "PayOnDelivery",
            self::SpotitGateAway => "Spotit",
            self::PesapalGateAway => "Pesapal",
            default => "N/A",
        };
    }

    public function getRequestTypeNameAttribute()
    {
        return match ($this->request_type)
        {
            self::SUBSCRIPTION_PURCHASE => "Subscription",
            self::BUYER_PURCHASE => "Order",
            self::BUSINESS_PAY_FOR_CAMPAIGN => "Campaign",
            default => "N/A",
        };
    }

    public function getTransactionStatusAttribute()
    {
        return match ($this->status ?? 0)
        {
            self::TransactionFailed => ["title" => "Failed", "class" => "eshop-failed"],
            self::WaitingForPayment => ["title" => "Waiting For Payment", "class" => "eshop-upcoming"],
            self::PaymentSuccessfully => ["title" => "Payment Successfully", "class" => "eshop-active"],
            self::RequestCancelledByUser => ["title" => "Request CancelledBy User", "class" => "eshop-failed"],
            self::RequestTimeout => ["title" => "Request Timeout", "class" => "eshop-failed"],
            self::TheBalanceIsInsufficientForTheTransaction => ["title" => "The Balance Is Insufficient", "class" => "eshop-failed"],
            default => ["title" => "Case Not Detected", "class" => "eshop-failed"],
        };
    }

    public static function getPhoneNumber($data)
    {
        if ($data['payment_type'] == 6) {
            return $data['spotit_phone_number'];
        }

        if ($data['payment_type'] == 8) {
            return $data['pesapal_phone_number'];
        }

        return $data['phone_number'];
    }
}

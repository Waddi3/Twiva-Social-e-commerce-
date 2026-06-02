<?php

namespace Modules\Order\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\Order;
use Modules\Payment\Services\MPESAService;

class OrderDeliveryRepository
{

    public function storeTransaction($order, $data)
    {
        $phone_number = str_replace('+', '', $data['mobile']);
        $phone_number = str_replace('-', '', $phone_number);
        $ordersAmount = $order ? $order->order_amount + $order->shipping_amount : 0;
        $transaction_details['transaction_amount'] = round($ordersAmount);
        // $transaction_details['user_id'] = Auth::guard('buyer')->id() ?? null;
        $transaction_details['user_id'] = $order?->buyer_id;
        $transaction_details['request_type'] = Transaction::BUYER_PURCHASE;
        $transaction_details['request_id'] = json_encode([$order->id]);
        $transaction_details['phone_number'] = $phone_number;
        $transaction_details['status'] = Transaction::WaitingForPayment;
        $gatewayResponse = '';

        list($gatewayResponse, $transaction_details) = $this->payWithMpesa($transaction_details);

        $transaction_details['transaction_details'] = json_encode($transaction_details);
        $transaction = Transaction::create($transaction_details);

        $order->transactions()->attach($transaction->id);
        return [$transaction, $gatewayResponse];
    }

    private function payWithMpesa(array $transaction_details): array
    {
        $gatewayResponse = MPESAService::createTransaction($transaction_details['transaction_amount'], $transaction_details['phone_number']);
        if (isset(json_decode($gatewayResponse)->errorMessage)) {
            $transaction_details['status'] = Transaction::TransactionFailed;
        }
        $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
        $transaction_details['transaction_id'] = json_decode($gatewayResponse)->MerchantRequestID ?? null;
        Session::put('transaction_id', json_decode($gatewayResponse)->MerchantRequestID ?? null);
        return array($gatewayResponse, $transaction_details);
    }
}

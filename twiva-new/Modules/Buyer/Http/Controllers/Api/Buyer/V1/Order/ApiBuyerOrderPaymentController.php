<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1\Order;

use Modules\Buyer\Http\Requests\Api\Buyer\V1\Order\ApiBuyerCheckPaymentStatusRequest;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\Order\ApiBuyerPaymentRequest;
use Modules\Buyer\Repositories\Order\ApiBuyerOrderRepository;
use Modules\Home\Entities\Transaction;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerOrderPaymentController
{
    public function __construct(public ApiBuyerOrderRepository $api_buyer_order_repository)
    {
    }

    public function pay(ApiBuyerPaymentRequest $request)
    {
        $data = $request->validated();
        $order_ids = array_map('intval', $data['order_ids']);

        $transaction = Transaction::whereJsonContains('request_id', $order_ids)->latest()->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found!', 'errors' => ['transaction' => 'Transaction not found!']], Response::HTTP_BAD_REQUEST);
        }

        $transaction->update([
            'transaction_id' => $data['transaction_id'],
        ]);

        $this->api_buyer_order_repository->checkTransactionStatus($transaction);

        return response()->json(['message' => 'Check status payment']);
    }

    public function checkOrderStatus(ApiBuyerCheckPaymentStatusRequest $request)
    {
        $data = $request->validated();
        if ($data['payment_type'] == 'PayOnDeliveryGateAway') {
            return response()->json(
                [
                    'transaction_status' => 1,
                ]
            );
        }


        // /// start force return success transaction for test
        // if ($data['transaction_id'] == 'TESTESTTEST') {
        //     return response()->json(
        //         [
        //             'transaction_status' => 1,
        //         ]
        //     );
        // }
        // /// end force return success transaction for test

        $transaction = Transaction::where('transaction_id', $data['transaction_id'])->first();

        logger('ApiBuyerOrderPaymentController:checkOrderStatus 61 => ', [
            'data' => $data,
            'transaction' => $transaction,
        ]);

        if (!$data['transaction_id'] || !$transaction || $transaction->status == Transaction::TransactionFailed) {
            return response()->json(
                [
                    'transaction_status' => Transaction::TransactionFailed,
                ]
            );
        }
        if ($transaction->status == Transaction::PaymentSuccessfully) {
            return response()->json(
                [
                    'transaction_status' => Transaction::PaymentSuccessfully,
                ]
            );
        }
        return response()->json(
            [
                'transaction_status' => Transaction::WaitingForPayment,
            ]
        );
    }
}

<?php


namespace Modules\Payment\Http\Controllers\API\V3;


use Modules\Home\Entities\Transaction;
use Modules\Payment\Http\Requests\CheckPaymentStatusRequest;

class PaymentController
{
    public function show(CheckPaymentStatusRequest $request)
    {
        $transaction = Transaction::where('transaction_id', request('transaction_id'))
            ->first();
        return response()->json(
            [
                'transaction_status' => $transaction->status,
                'transaction_message' => $transaction->status ? 'Paid' : 'not Paid',
                'data' => $transaction,
            ]);

    }

}
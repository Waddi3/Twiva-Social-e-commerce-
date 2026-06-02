<?php


namespace Modules\Payment\Http\Controllers\API\V3;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Campaign\Entities\PostPrice;
use Modules\Home\Entities\Transaction;
use Modules\Payment\Http\Requests\CreateMPESATransactionRequest;
use Modules\Payment\Services\MPESAService;
use Modules\Payment\Services\PaymentService;
use function auth;

// This Class is used to  pay orders for buyer
class MPESAController
{
    public function createTransaction(CreateMPESATransactionRequest $request)
    {
        $data = $request->validated();

        if ($data['request_type'] == Transaction::BUSINESS_PAY_FOR_CAMPAIGN)
        {
            foreach ($data['details'] as $newPrice)
            {
                PostPrice::where('post_id', $data['request_id'])
                    ->where('infulancer_id', $newPrice['id'])->update(['final_price' => $newPrice['price']]);
            }
            $transaction_details['transaction_amount'] = collect($data['details'])->sum('price');
            $transaction_details['transaction_amount'] = round($transaction_details['transaction_amount'] + (($transaction_details['transaction_amount'] * 15) / 100));
        }
        else
        {
            $transaction_details['transaction_amount'] = $data['amount'];
        }
        //Transaction Create
        $transaction_details['user_id'] = auth('api')->id();
        $transaction_details['request_type'] = $data['request_type'];
        $transaction_details['request_id'] = json_encode($data['request_id']);
        $transaction_details['phone_number'] = $data['phone_number'];
        $mpesaResponse = json_decode(MPESAService::createTransaction($transaction_details['transaction_amount'], $data['phone_number']));

        if (isset($mpesaResponse->errorMessage))
        {
            $transaction_details['status'] = Transaction::TransactionFailed;
            $transaction_details['transaction_details'] = json_encode($data['details']);
            $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
            $transaction = Transaction::create($transaction_details);
            return response()->json(['message' => $mpesaResponse->errorMessage], 403);
        }
        else
        {
            $transaction_details['transaction_id'] = $mpesaResponse->MerchantRequestID;
        }
        $transaction_details['transaction_details'] = json_encode($data['details']);
        $transaction_details['payment_gateway_type'] = Transaction::MPESAGateAway;
        $transaction = Transaction::create($transaction_details);
        return response()->json(['message' => 'Transaction is await for payment', 'amount' => $transaction->transaction_amount, 'transaction_id' => $transaction->transaction_id]);
    }

    public function callback(Request $request)
    {
        $data = request()->all();
        if (isset($data['Body']))
        {
            $responseCode = $data['Body']['stkCallback']['ResultCode'];
            $transactionID = $data['Body']['stkCallback']['MerchantRequestID'];
            $transaction = Transaction::where('transaction_id', $transactionID)->first();
            // TODO::H group_buying check orders have this status
            if (!$transaction)
            {
                return "fail";
            }
            if ($responseCode == 0)
            {
                PaymentService::responseToSuccessfulPayment($transactionID);
            }
            else
            {
                MPESAService::responseToFailedPayment($transactionID, $responseCode);
            }
            return response()->json(['ok']);
        }
        Log::debug('MpesaCallback', ['request' => $data]);
        return "fail";
    }


}

<?php


namespace Modules\Payment\Http\Controllers\API\V3;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Campaign\Entities\PostPrice;
use Modules\Home\Entities\Transaction;
use Modules\Payment\Http\Requests\CreateFlutterwaveTransactionRequest;
use Modules\Payment\Services\FlutterWaveService;
use Modules\Payment\Services\PaymentService;

class FlutterWaveController
{

    public function createTransaction(CreateFlutterwaveTransactionRequest $request)
    {
        $transaction = $this->storeTransactionInDB($request);
        return response()->json(['message' => 'Transaction is await for payment', 'amount' => $transaction->transaction_amount, 'transaction_id' => $transaction->transaction_id]);
    }

    public function createWebTransaction(CreateFlutterwaveTransactionRequest $request)
    {
        $transaction = $this->storeTransactionInDB($request);
        $transaction_details = json_decode($transaction->transaction_details);
        $request_id = intval(str_replace('"', "", $transaction->request_id));
        $transaction_details['flutterwave_payment_url'] = config('twiva.bases.app_url') . "/#/business/campaigns/$request_id";
        $transaction->update(['transaction_details' => json_encode($transaction_details)]);
        list($transactionId, $paymentLink) = FlutterWaveService::createTransactionForCampaignPayments($transaction, auth('api')->user());
        return response()->json(['paymentLink' => $paymentLink, 'transaction_id' => $transactionId]);
    }

    public function callback(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->tx_ref)->firstOrFail();
        if (FlutterWaveService::verfiyTransaction($transaction, $request->transaction_id)) {
            PaymentService::responseToSuccessfulPayment($request->tx_ref);
            if ($transaction->request_type == Transaction::BUYER_PURCHASE) {
                return redirect(json_decode($transaction->transaction_details)->flutterwave_payment_url);
            }
            if ($transaction->request_type == Transaction::BUYER_PURCHASE_GROUP_PAY) {
                return redirect(json_decode($transaction->transaction_details)->flutterwave_payment_url);
            }
            if ($transaction->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN && (json_decode($transaction->transaction_details ?? "")?->flutterwave_payment_url ?? false)) {
                return redirect(json_decode($transaction->transaction_details)->flutterwave_payment_url);
            }
            return response(200);
        }
        else {
            $transaction->update(['status' => Transaction::TransactionFailed]);
        }
        if ($transaction->request_type == Transaction::BUYER_PURCHASE) {
            return redirect(json_decode($transaction->transaction_details)->flutterwave_payment_url);
        }
        if ($transaction->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN && (json_decode($transaction->transaction_details ?? "")?->flutterwave_payment_url ?? false)) {
            return redirect(json_decode($transaction->transaction_details)->flutterwave_payment_url);
        }
        return response(403);
    }

    public function storeTransactionInDB(CreateFlutterwaveTransactionRequest $request): Transaction
    {
        $data = $request->validated();
        $transaction_details['user_id'] = auth('api')->id();
        $transaction_details['request_type'] = $data['request_type'];// for buyer purchase
        $merchantRequestID = Transaction::generateMerchantRequestID();
        $transaction_details['request_id'] = json_encode($data['request_id']);
        $transaction_details['phone_number'] = $data['phone_number'];
        $transaction_details['transaction_id'] = $merchantRequestID;
        $transaction_details['transaction_details'] = json_encode($data['details']);
        $transaction_details['payment_gateway_type'] = Transaction::FlutterWaveGateAway;
        if ($data['request_type'] == Transaction::BUSINESS_PAY_FOR_CAMPAIGN) {
            foreach ($data['details'] as $newPrice) {
                PostPrice::where('post_id', $data['request_id'])
                    ->where('infulancer_id', $newPrice['id'])->update(['final_price' => $newPrice['price']]);
            }
            $transaction_details['transaction_amount'] = collect($data['details'])->sum('price');
            $transaction_details['transaction_amount'] = $transaction_details['transaction_amount'] + (($transaction_details['transaction_amount'] * 15) / 100);
        }
        else {
            $transaction_details['transaction_amount'] = $data['amount'];
        }
        return Transaction::create($transaction_details);
    }


}

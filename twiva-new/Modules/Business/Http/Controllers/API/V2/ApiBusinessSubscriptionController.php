<?php

namespace Modules\Business\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Home\Entities\Transaction;
use Modules\Home\Http\Controllers\MpesaController;
use Modules\User\Entities\Subscription;

class ApiBusinessSubscriptionController extends Controller
{
    public function purchaseSubscription(Request $request)
    {
        $user = auth('api')->user();
        if ($user) {
            try {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'subscription_id' => 'required',
                    'phone_number' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'error' => $validator->messages()->first()], 400);
                }
                $subscription_details = Subscription::find($data['subscription_id']);
                if ($subscription_details) {

                    $subscription_amount = $subscription_details->price;
                    $phone_number = $data['phone_number'];

                    $mpesaObj = new MpesaController();
                    $tansaction_details = $mpesaObj->customerMpesaSTKPush($subscription_amount, $phone_number);
                    $transactionResponse = json_decode($tansaction_details);
                    if (isset($transactionResponse->errorMessage)) {
                        $error_message = array('status' => false, 'type' => 'transaction', 'message' => $transactionResponse->errorMessage);
                        return response()->json($error_message, 400);
                    }
                    $transaction_details['user_id'] = $user->id;
                    $transaction_details['request_type'] = 1;// for subscription
                    $transaction_details['request_id'] = $data['subscription_id'];
                    $transaction_details['phone_number'] = $data['phone_number'];
                    $transaction_details['transaction_id'] = $transactionResponse->MerchantRequestID;
                    $transaction_details['transaction_amount'] = $subscription_amount;
                    $transaction_details['transaction_details'] = $tansaction_details;
                    Transaction::create($transaction_details);
                    return response()->json(['status' => true, 'message' => 'Request Sent to user', 'merchant_request_id' => $transactionResponse->MerchantRequestID], 200);
                } else {
                    $error_message = array('status' => false, 'message' => "No subscriptions found");
                    return response()->json($error_message, 400);
                }
            } catch (Exception $e) {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        } else {
            $error_message = array('status' => false, 'message' => "Unauthorized");
            return response()->json($error_message, 401);
        }

    }
}

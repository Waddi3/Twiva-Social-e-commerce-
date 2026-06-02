<?php

namespace Modules\Payment\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class PayoutPaymentService
{

    private static function BusinessToCustomerGenerateRemarks(string $remarks, $winning_payout)
    {
        if (Str::length($remarks) == 0) {
            $remarks = "Payment for user by id {$winning_payout->user_id} and amount {$winning_payout->transaction_amount} and phone {$winning_payout->phone_number}.";
        }
        return $remarks;
    }

    private static function BusinessPayBillGenerateRemarks(string $remarks, $winning_payout)
    {
        if (Str::length($remarks) == 0) {
            $remarks = "Payment for user by id {$winning_payout->user_id} and amount {$winning_payout->transaction_amount} and till number {$winning_payout->till_number}.";
        }
        return $remarks;
    }

    public static function processPayoutPayment($winning_payout, $remarks = "")
    {
        if( !is_null($winning_payout->phone_number) ) {
            $remarks = self::BusinessToCustomerGenerateRemarks($remarks, $winning_payout);
            $response = MPESAService::BusinessToCustomer($winning_payout->mobile, $winning_payout->transaction_amount, 'BusinessPayment', $remarks);
        }
        else {
            $remarks = self::BusinessPayBillGenerateRemarks($remarks, $winning_payout);
            $response = MPESAService::BusinessPayBill($winning_payout->till_number, $winning_payout->transaction_amount, 'BusinessPayBill', $remarks);
        }

        $response_body = json_decode($response->body());
        $winning_payout->update([
            'originator_conversation_id' => $response_body->OriginatorConversationID ?? null,
            'transaction_response' => $response_body,
        ]);
        return $response;
    }


    private static function handelResponseRequest(PromiseInterface|Response $response_request)
    {
        $error_code = $response_request->errorCode ?? null;
        $response_code = $response_request->ResponseCode ?? null;
        if ($response_code) {
            $response = (object)[
                'status' => true,
                'ConversationID' => $response_code->ConversationID,
                'OriginatorConversationID' => $response_code->OriginatorConversationID,
                'ResponseCode' => $response_code->ResponseCode,
                'ResponseDescription' => $response_code->ResponseDescription,
                'requestId' => $response_code->ResponseDescription,
                'errorCode' => $response_code->ResponseDescription,
                'errorMessage' => $response_code->ResponseDescription,
            ];
        }
    }
}

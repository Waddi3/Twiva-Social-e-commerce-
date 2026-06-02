<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Modules\Home\Entities\Transaction;
use Modules\Payment\Services\PaymentService;
use Modules\Payment\Services\SpotitService;

class SpotitPaymentController extends Controller
{
    public function __construct(public SpotitService $spotit_service) {}

    public function fetchListOfBanks()
    {
        return $this->spotit_service->fetchListOfBanks();
    }

    public function callbackUrl()
    {
        $data = request()->all();
        logger("SpotitPaymentController 20 => ", ['date' => $data]);
        if (isset($data['Body'])) {
            try {
                $spotit_callback = $data['Body']['SpotitCallback'];
                // $merchant_code = $spotit_callback['MerchantCode'];
                $spotit_checkout_code = $spotit_callback['SpotitCheckoutCode'];
                $result_code = $spotit_callback['ResultCode'];

                $transaction = Transaction::query()
                    ->where('transaction_id', $spotit_checkout_code)
                    // ->where('originator_conversation_id', $merchant_code)
                    ->first();
                if (!$transaction) {
                    return;
                }
                if ($result_code == 200) {
                    PaymentService::responseToSuccessfulPayment($transaction);
                } else {
                    $transaction->update(['status' => Transaction::TransactionFailed]);
                }
                $transaction_details = json_decode($transaction->transaction_details, true);
                $transaction_details['callback-response'] = $data;
                $transaction->update(['transaction_details' => json_encode($transaction_details)]);
            } catch (Exception $exception) {
                logger("Exception SpotitPaymentController.php 46 => ", [
                    'exception-message' => $exception->getMessage(),
                    'exception' => $exception,
                ]);
            }
        }
    }
}

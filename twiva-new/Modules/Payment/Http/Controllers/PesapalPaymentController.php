<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Modules\Home\Entities\Transaction;
use Modules\Payment\Services\PaymentService;
use Modules\Payment\Services\PesapalService;

class PesapalPaymentController extends Controller
{
    public function __construct(public PesapalService $pesapal_service) {}

    public function callbackUrl()
    {
        $data = request()->all();
        logger("PesapalPaymentController 18 => ", ['date' => $data]);
        try {
            if (array_key_exists('OrderTrackingId', $data)) {
                $order_tracking_id = $data['OrderTrackingId'];
                $transaction = Transaction::query()
                    ->where('transaction_id', $order_tracking_id)
                    ->first();
                if ($transaction) {
                    $response = $this->pesapal_service->getTransactionStatus($order_tracking_id);
                    /**
                     * status_code
                     *      Pesapal status code representing the payment_status_description.
                                0 - INVALID
                                1 - COMPLETED
                                2 - FAILED
                                3 - REVERSED
                     */
                    if ($response['status_code'] == 1) {
                        PaymentService::responseToSuccessfulPayment($transaction);
                    } else {
                        $transaction->update(['status' => Transaction::TransactionFailed]);
                    }
                    $transaction_details = json_decode($transaction->transaction_details, true);
                    $transaction_details['callback-response'] = $data;
                    $transaction->update(['transaction_details' => json_encode($transaction_details)]);

                    return redirect()->to(json_decode($transaction->transaction_details, true)['confirm_url']);
                }
            }
        } catch (Exception $exception) {
            logger("Exception SpotitPaymentController.php 46 => ", [
                'exception-message' => $exception->getMessage(),
                'exception' => $exception,
            ]);
        }

        return redirect()->route('ecommerce.index');
    }
}

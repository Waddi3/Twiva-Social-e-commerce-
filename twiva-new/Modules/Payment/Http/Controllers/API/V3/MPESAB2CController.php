<?php


namespace Modules\Payment\Http\Controllers\API\V3;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Campaign\Entities\PostPrice;
use Modules\Home\Entities\Transaction;
use Modules\Notification\Notifications\Influencer\WinningPayoutNotification;
use Modules\Payment\Entities\WinningPayout;
use Modules\Payment\Services\MPESAService;
use Modules\Payment\Services\PaymentService;

// This Class is used to  pay orders for buyer
class MPESAB2CController
{

    /**
     * https://developer.safaricom.co.ke/APIs/BusinessToCustomer
     */
    public function queue(Request $request)
    {
        // TODO::H check this response from mpesa side
        logger("Modules/Payment/Http/Controllers/API/V3/MPESAB2CController.php:19 => queue => ", ['$request' => $request->all()]);
    }

    public function result(Request $request)
    {
        $response = $request->all();
        $result = $response['Result'] ?? null;
        if (is_null($result)) {
            return false;
        }
        $result_code = $result['ResultCode'] ?? null;
        if (is_null($result_code)) {
            return false;
        }
        $originator_conversation_id = $result['OriginatorConversationID'] ?? null;
        $transaction_id = $result['TransactionID'] ?? null;
        if (!$originator_conversation_id) {
            return false;
        }
        $winning_payout = WinningPayout::with('user')
            ->where('originator_conversation_id', $originator_conversation_id)
            ->where(function (Builder $statusQuery)
            {
                $statusQuery->where('status', WinningPayout::STATUS_INIT)
                    ->orWhere('status', WinningPayout::STATUS_IN_PROGRESS);
            })
            ->first();
        if ($result_code == 0) {
            // success
            $winning_payout_status = WinningPayout::STATUS_SUCCESS;
        } else {
            // error
            $winning_payout_status = WinningPayout::STATUS_FAIL;
        }

        if ($winning_payout) {
            $winning_payout->update([
                'transaction_id' => $transaction_id,
                'status' => $winning_payout_status,
                'transaction_details' => $response,
            ]);
            \Log::info('PayoutPaymentService::processPayoutPayment callback',['$winningPayout' => $winning_payout]);
            // notify user and admin
            $user = $winning_payout->user;
            $user->notify(new WinningPayoutNotification($winning_payout));
        }
    }
}

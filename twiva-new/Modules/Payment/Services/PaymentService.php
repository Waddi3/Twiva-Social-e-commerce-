<?php


namespace Modules\Payment\Services;


use Illuminate\Support\Facades\Log;
use Modules\Business\Entities\BusinessTransactionsHistory;
use Modules\Campaign\Repository\Business\CampaignRepository;
use Modules\Home\Entities\Transaction;
use Modules\Shop\Repositories\Influencer\ShopRepository;

class PaymentService {
    public static function responseToSuccessfulPayment($transaction_id)
    {
        if ($transaction_id instanceof Transaction)
        {
            $transaction = $transaction_id;
        }
        else
        {
            $transaction = Transaction::where('transaction_id', $transaction_id)->first();
        }

        self::payTransactionRequest($transaction);

        if ($transaction->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN)
        {
            self::storeBusinessTransaction($transaction);
        }
        $transaction->update(['status' => Transaction::PaymentSuccessfully]);
        Log::debug("Transaction with id {$transaction->id} paid Successfully");
        return true;
    }

    private static function payTransactionRequest($transaction): void
    {
        switch ($transaction->request_type)
        {
            case Transaction::BUSINESS_PAY_FOR_CAMPAIGN:
                CampaignRepository::payForCampaign($transaction->request_id);
                break;
            case Transaction::BUYER_PURCHASE:
                ShopRepository::payForOrder($transaction);
                break;
            case Transaction::BUYER_PURCHASE_GROUP_PAY:
                ShopRepository::groupPayForOrder($transaction);
                break;
            case Transaction::SUBSCRIPTION_PURCHASE:
                Log::debug('Transaction::SUBSCRIPTION_PURCHASE');
                break;

        }
    }

    private static function storeBusinessTransaction($transaction, $isPaid = 1)
    {
        BusinessTransactionsHistory::create(
            [
                'user_id'             => $transaction->user_id,
                'transaction_amount'  => $transaction->transaction_amount,
                'transaction_details' => json_encode($transaction),
                'request_type'        => $transaction->request_type,
                'request_id'          => intval(str_replace('"', "", $transaction->request_id)),
                'is_paid'             => $isPaid,
                'is_active'           => 0
            ]
        );
    }

    public static function responseToFailedPayment($transaction_id)
    {
        $transaction = Transaction::where('transaction_id', $transaction_id)->first();

        if ($transaction && $transaction->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN)
        {
            self::storeBusinessTransaction($transaction, 0);
            $transaction->update(['status' => Transaction::TransactionFailed]);
        }
        return true;
    }


}

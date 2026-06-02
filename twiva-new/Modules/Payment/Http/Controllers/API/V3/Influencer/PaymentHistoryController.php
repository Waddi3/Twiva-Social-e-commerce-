<?php

namespace Modules\Payment\Http\Controllers\API\V3\Influencer;

use Illuminate\Routing\Controller;
use Modules\Influencer\Entities\InfluencerTransactionsHistory;
use Modules\Payment\Http\Resources\Influencer\PaymentHistoryIndexCollection;
use function auth;

class PaymentHistoryController extends Controller
{
    public function index()
    {
        $payment_history = InfluencerTransactionsHistory::select('user_id', 'transaction_amount', 'request_type', 'request_id', 'created_at')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'DESC');
        if (filter_var(request('campaign-earnings'), FILTER_VALIDATE_BOOLEAN))
        {
            $payment_history = $payment_history->where('request_type', InfluencerTransactionsHistory::BUSINESS_PAY_FOR_CAMPAIGN);
        }
        else
        {
            $payment_history = $payment_history->where('request_type', InfluencerTransactionsHistory::BUYER_PURCHASE);
        }
        return response()->json(['user_id' => auth('api')->id(), 'data' => PaymentHistoryIndexCollection::collection($payment_history->paginate(10))->resource]);
    }

}

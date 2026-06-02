<?php

namespace Modules\Payment\Http\Controllers\API\V3\Business;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Business\Entities\BusinessTransactionsHistory;
use Modules\Payment\Http\Resources\Business\PaymentHistoryIndexCollection;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $paymentHistory = BusinessTransactionsHistory::select('user_id', 'transaction_amount', 'request_type', 'request_id', 'created_at')
            ->where('user_id', auth('api')->id())
            ->orderBy('id', 'DESC')->paginate(10);
        return response()->json(['data' => PaymentHistoryIndexCollection::collection($paymentHistory)->resource], 200);
    }
}

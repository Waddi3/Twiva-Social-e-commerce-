<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Home\Entities\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->paginate(10);

        return view('admin::transactions.index', [
            'transactions' => $transactions,
            'transactionTypes' => Transaction::TRANSACTION_TYPES,
            'transactionStatuses' => Transaction::TransactionStatuses,
        ]);
    }

    public function filter(Request $request)
    {
        if (!$request->ajax())
        {
            return "No Access";
        }
        $pageName = $request->get('pagename');
        $sort_by = $request->get('sortby');
        $keyword = $request->get('query');
        $transaction_type = $request->get('transaction_type');
        $transaction_status = $request->get('transaction_status');
        $q = Transaction::select();
        $q = $q->orderBy($sort_by, 'desc');
        if (strlen($keyword))
        {
            $q->where('phone_number', 'like', "%$keyword%")
                ->orwhere('id', 'like', "%$keyword%")
                ->orwhere('transaction_id', 'like', "%$keyword%")
                ->orwhere('transaction_amount', 'like', "%$keyword%")
                ->orWhereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where("name", 'like', "%$keyword%");
                });
        }
        if (!empty($transaction_type))
        {
            $q->where('request_type', $transaction_type);
        }
        if (!is_null($transaction_status))
        {
            $q->where('status', $transaction_status);
        }

        return view('admin::transactions.data', ['transactions' => $q->paginate(10)])->render();
    }


}

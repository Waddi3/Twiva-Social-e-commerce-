<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Modules\Buyer\Entities\Buyer;
use function request;

class BuyerController extends Controller
{

    public function index()
    {
        $buyers = Buyer::withTrashed()/*->whereHas('orders')*/ ->orderBy('id', 'desc')->paginate(10);
        $buyers = $buyers->setCollection($buyers->getCollection()->sortBy('ordersCount'));
        return view('admin::buyer.index', ['users' => $buyers]);
    }

    public function toggleActiveStatus()
    {
        $buyer = Buyer::withTrashed()->where('id', request('buyer_id'))->first();
        if ($buyer->trashed()) {
            $buyer->restore();
        } else {
            $buyer->delete();
        }
        return response(['message' => 'buyer has been deleted']);

    }

}

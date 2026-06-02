<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;

use function Clue\StreamFilter\fun;

class OrderController extends Controller
{
    public function index()
    {
        $orders = $this->getOrdersBasicQuery()
            ->paginate(10);
        return view('admin::orders.index', ['orders' => $orders, 'statues' => Order::STATUSES]);
    }


    public function show($id)
    {
        $order = Order::where('id', $id)->withTrashed()
            ->with(
                [
                    'business' => function ($businessQuery) {
                        $businessQuery->withTrashed();
                    },
                    'orderDetails' => function ($orderDetails) {
                        $orderDetails->withTrashed()
                            ->with([
                                'productDetails' => function ($productDetailsQuery) {
                                    $productDetailsQuery->withTrashed()
                                        ->with([
                                            'actualDetail' => function ($actualDetailQuery) {
                                                $actualDetailQuery->withTrashed()->with(
                                                    [
                                                        'business' => function ($businessQuery) {
                                                            $businessQuery->withTrashed();
                                                        },
                                                    ]
                                                );
                                            }
                                        ]);
                                },
                                'businessProductDetails' => function ($businessProductDetails) {
                                    return $businessProductDetails->withTrashed()
                                        ->with([
                                            'user' => function ($user) {
                                                return $user->withTrashed();
                                            }
                                        ]);
                                }
                            ]);
                    }
                ]
            )
            ->first();
        return view(
            'admin::orders.show',
            [
                'order' => $order
            ]
        );
    }

    // Generate PDF
    public function createPDF($id)
    {
        // retreive all records from db
        $order = Order::with('shippingAddress', 'influencerInfo', 'orderDetails')->where('id', $id)->first();

        // share data to view
        view()->share('order', $order);
        $pdf = PDF::loadView('admin::pdf.order_details', array('order' => $order));

        // download PDF file with download method
        return $pdf->download('orders.pdf');
    }

    public function filter(Request $request)
    {
        if (!$request->ajax()) {
            return "No Access";
        }
        $status = $request->get('status');
        $query = $request->get('query');
        $q = $this->getOrdersBasicQuery();

        if (strlen($status) && $status != 9) {
            $q->where('order_status', '=', $status);
        }
        if (strlen($query) >= 1) {
            $q->where(function ($qq) use ($query) {
                $qq->where('id', $query)
                    ->orWhereHas('shop.influencer', function ($que) use ($query) {
                        $que->where('name', 'like', "%$query%");
                    })
                    ->orWhereHas('buyer', function ($que) use ($query) {
                        $que->where('name', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('shippingAddress', function ($que) use ($query) {
                        $que->where(DB::raw("concat(first_name, ' ', last_name)"), 'like', "%$query%");
                    });
            });
        }
        return view('admin::orders.data', [
            'orders' => $q->orderBy('id')->paginate(10)
        ])->render();
    }

    private function getOrdersBasicQuery()
    {
        return Order::with('latestTransaction', 'latestTransaction.user', 'shippingAddress', 'influencerInfo', 'orderDetails', 'buyer', 'influencer')
            //            ->where('payment_status', '>', 0)
            ->orderBy('id', 'desc')
            ->whereHas('shippingAddress');
    }
}

<?php


namespace Modules\Buyer\Http\Controllers\ECommerce;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Home\Entities\Category;
use Modules\Order\Entities\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Doctrine\DBAL\Driver\Middleware;
use Modules\Order\Entities\OrderDetails;
use Modules\User\Entities\User;
class ECommerceOrderController {
    public function index($account = null)
    {
        $orders = Order::where('buyer_id', Auth::guard('buyer')->id())->orderByDesc('id')->get();
        $categories = Category::with('subcategories')->latest()->get();
        return view('front::buyer.index', [
            'orders'     => $orders,
            'categories' => $categories,
        ]);
    }

    public function show($order)
    {
        $order = Order::where(['id' => $order])->with('groupPaymentRequest')->firstOrFail();
        return response()->json(['view' => view('front::buyer._details', ['order' => $order])->render()]);
    }

    public function downloadReceipt($order)
    {
        $order = Order::where('id', $order)->with([
            'orderDetails',
            'buyerData',
            'shippingAddress',
            'transactions',
            'shop',
            'influencerInfo',
            'orderList',
        ])->where('buyer_id',auth('buyer')->id())->first();

        $orderItems = OrderDetails::where('id', $order)->with([
            'productDetails',
            'businessProductDetails'
        ])->first();

        $productDetails = User::where('id', $order->user_id)->with([
            'businessProducts',
            'products',
            'InfluencerDetails',
            'influencerShopProducts'
        ])->first();
        if (!$order)
        {
            abort(403);
        }

        $pdf = Pdf::loadView('buyer::invoice.pdf.receipt', [
            'order' => $order,
            'orderItems' => $orderItems,
            'productDetails' =>$productDetails,
        ]);
        return $pdf->download('Receipt_yourOrder' . $order->id . '.pdf');
    }


}

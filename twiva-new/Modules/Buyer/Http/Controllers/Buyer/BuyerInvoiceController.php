<?php

namespace Modules\Buyer\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Modules\Buyer\Entities\Invoice;
use Session;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;

class BuyerInvoiceController extends Controller
{
    public function checkout($slug)
    {
        $invoice = Invoice::with('user.shop', 'buyer', 'products')->slug($slug)->invoice()->first();
        if (!$invoice) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $shop_domain = $invoice->user->shop->domain;
        if ($invoice->status != Invoice::PENDING_STATUS) {
            $link = route('shop.product.index', ['account' => $shop_domain]);
            return view('buyer::invoice.already-purchased', ['link' => $link]);
        }
        // TODO::H check due_date
        $cart = Invoice::prepareCart($invoice);
        Session::put($cart);
        return redirect()->route('buyer.checkout.account', ['account' => $shop_domain, 'cart' => $cart, 'invoice' => $invoice->getAttributes()]);
    }

}

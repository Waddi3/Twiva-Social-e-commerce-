<?php

namespace Modules\Buyer\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Modules\Buyer\Entities\Invoice;
use Session;

class BuyerExpressController extends Controller
{
    public function checkout($slug)
    {
        $express = Invoice::with('user.shop', 'buyer', 'products')->slug($slug)->express()->first();
        $shop_domain = $express->user->shop->domain;
        if ($express->status != Invoice::PENDING_STATUS)
        {
            $link = route('shop.product.index', ['account' => $shop_domain]);
            return view('buyer::invoice.already-purchased', ['link' => $link]);
        }
        $cart = Invoice::prepareCart($express);
        Session::put('cart', $cart);
        return redirect()->route('buyer.checkout.account', ['account' => $shop_domain, 'cart' => $cart, 'invoice' => $express->getAttributes()]);
    }
}

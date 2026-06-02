<?php

namespace Modules\Shop\Http\Controllers\ECommerce;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Buyer\Entities\Invoice;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\BuyerOrderGroupPayment;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderGroupPaymentRequest;
use Modules\Order\Entities\ShippingAddress;
use Modules\Payment\Services\SpotitService;
use Modules\Shop\Http\Requests\ShopSite\CheckoutStoreAddressRequest;
use Modules\Shop\Http\Requests\ShopSite\GroupPayRequest;
use Modules\Shop\Http\Requests\ShopSite\PayRequest;
use Modules\Shop\Repositories\ECommerce\ECommerceInfluencerBuyerRepository;
use Modules\Shop\Repositories\ECommerce\OrderGroupPayRepository;
use Modules\Shop\Repositories\Influencer\ShopRepository;
use Modules\Shop\Services\ECommerceShopProductService;
use Modules\Shop\Services\ShopProductService;
use Modules\User\Entities\User;

class OrderGroupPayController extends Controller {
    private readonly ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository;

    public function __construct(private readonly OrderGroupPayRepository $groupPayRepository)
    {
    }

    public function paymentPage($uuid)
    {
        $orderGroupPaymentRequest = OrderGroupPaymentRequest::where('uuid', $uuid)
            ->with(['orders', 'buyerOrderGroupPayment'])
            ->firstOrFail();
        if (!$orderGroupPaymentRequest->remaining_amount){
            return redirect('/')->with(['errorMessage' => 'Order Already paid']);
        }
        Session::forget('cart');
        Session::put('cart', $orderGroupPaymentRequest->cart);
        Session::remove('transaction_id');
        return view('front::checkout.groupPayment',
            $this->groupPayRepository->getPaymentPageData($orderGroupPaymentRequest));
    }

    public function pay(GroupPayRequest $request, $uuid)
    {
        Session::remove('transaction_id');
        Session::remove('payment_type');
        removeCouponSessionCache();

        $data = $request->validated();
        $orderGroupPaymentRequest = OrderGroupPaymentRequest::where('uuid', $uuid)
            ->with(['orders', 'buyerOrderGroupPayment'])
            ->first();

        return $this->groupPayRepository->createBuyerGroupPayRequest(
            orderGroupPaymentRequest: $orderGroupPaymentRequest,
            amountPaymentGroupBuying: $data['amount_payment_group_buying'],
            buyerId: \auth('buyer')->id(),
            paymentType: $data['payment_type'],
            fullData: $data
        );
    }
}

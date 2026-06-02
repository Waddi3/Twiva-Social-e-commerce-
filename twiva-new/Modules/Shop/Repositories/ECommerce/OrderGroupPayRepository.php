<?php

namespace Modules\Shop\Repositories\ECommerce;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\BuyerOrderGroupPayment;
use Modules\Order\Entities\OrderGroupPaymentRequest;

class OrderGroupPayRepository {
    public function __construct(private readonly ECommerceInfluencerBuyerRepository $e_commerce_buyer_repository)
    {
    }

    public function getRequestData(OrderGroupPaymentRequest $orderGroupPaymentRequest): array
    {
        return [
            'items_total'         => $orderGroupPaymentRequest->orders->sum('order_amount'),
            'shipping_fee'        => $orderGroupPaymentRequest->orders->sum('shipping_amount'),
            'total'               => $orderGroupPaymentRequest->amount,
            'amount_already_paid' => $orderGroupPaymentRequest->all_paid_amount,
            'your_contribution'   => $orderGroupPaymentRequest->buyerOrderGroupPayment->whereNotNull('paid_at')
                ->where('buyer_id', auth('buyer')->id())
                ->sum('amount'),
            'amount_pending'      => $orderGroupPaymentRequest->amount - $orderGroupPaymentRequest->all_paid_amount,
        ];

    }

    public function getPaymentPageData(OrderGroupPaymentRequest $orderGroupPaymentRequest): array
    {
        return [
            'productsData' => $this->e_commerce_buyer_repository->getProductFromSession(),
            'requestData'  => $this->getRequestData($orderGroupPaymentRequest)
        ];
    }

    public function createBuyerGroupPayRequest(
        OrderGroupPaymentRequest $orderGroupPaymentRequest,
        string                   $amountPaymentGroupBuying,
        string                   $buyerId,
        string                   $paymentType,
        mixed                    $fullData
    ) {
        $orders = $orderGroupPaymentRequest->orders;
        BuyerOrderGroupPayment::create([
            'request_id' => $orderGroupPaymentRequest->id,
            'amount'     => $amountPaymentGroupBuying,
            'buyer_id'   => $buyerId,
        ]);
        Session::put('payment_type', $paymentType);
        Session::put('address_id', $orderGroupPaymentRequest->orders[0]->shippingAddress->id);
        Session::put('orders_ids', $orderGroupPaymentRequest->orders->pluck('id')->toArray());

        if ($paymentType == Transaction::MPESAGateAway)
        {
            list($transaction, $response) = $this->e_commerce_buyer_repository->storeTransaction($orders, $fullData);
            if (isset(json_decode($response)->errorMessage))
            {
                Session::put('transaction_id', $transaction->transaction_id ?? null);
                return redirect()->route('e-commerce.buyer.checkout.payment')->with(['errorMessage' => json_decode($response)->errorMessage]);
            }
        }
        if ($paymentType == Transaction::FlutterWaveGateAway)
        {
            list($MerchantRequestID, $redirectLink) = $this->e_commerce_buyer_repository->storeTransaction($orders, $fullData);
            return Redirect::to($redirectLink);
        }
    }


}
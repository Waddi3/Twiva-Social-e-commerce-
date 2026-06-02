<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;
use Modules\Order\Entities\Order;
use Modules\Order\Respositories\Business\OrderRepository;
use Modules\User\Entities\User;

class ShowOrderResource extends ResourceCollection
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
        parent::__construct($order);
    }

    public function getShippingAddressDetails($shippingAddress): object
    {
        $data['address'] = $shippingAddress->fullAddress;
        $data['name'] = $shippingAddress->contactName;
        $data['phone_number'] = $shippingAddress->phone_number;
        $data['latitude'] = $shippingAddress->latitude;
        $data['longitude'] = $shippingAddress->longitude;
        $data['email'] = $shippingAddress->email ?? '-';
        return (object)$data;
    }

    public function toArray($request)
    {
        $data = [
            'id' => $this->order->id,
            'order_number' => $this->order->id,
            'status' => $this->order->statusString,
            'buyer_email' => $this->order->shippingAddress->email ?? '-',
            'shipping' => $this->order->shipping_amount,
            'sub_total' => OrderRepository::calculateBusinessAmountFromOrder($this->order),
            //            'net_total' => OrderRepository::calculateBusinessAmountFromOrderWithShipping($this->order),
            'net_total' => $this->order->BusinessAmount,
            'order_date' => $this->order->created_at->format('d M, Y'), // , H:i a
            'shipping_address' => $this->getShippingAddressDetails($this->order->shippingAddress),
            'payment_status' => ucfirst(strtolower(Order::PAYMENT_STATUS[$this->order->payment_status])),
            'paymentMethod' => $this->order->latestTransaction[0]?->gatewayName ?? "",
            'paymentStatus' => $this->order->latestTransaction[0]?->transactionStatus['title'] ?? "",
            //            'transactions' => $this->getTransactions($this->order->transactions),
            'products' => $this->order->orderDetails->map(function ($details) {
                //                $details === OrderDetails
                if ($details->productDetails) {
                    $details->amount = $details->businessAmount;
                    if ($this->order->shop->user->type == User::InfluencerType) {
                        $details->id = $details->productDetails->actualDetail()->withTrashed()->first()->id ?? "";
                        $details->title = $details->productDetails->actualDetail()->withTrashed()->first()->name ?? "";
                        $details->image = $details->productDetails->productMainImage->imageUrl ?? "";
                        $details->brand = $details->productDetails->actualDetail()->withTrashed()->first()->brand ?? "";
                        $details->is_ali_express = $details->productDetails->actualDetail()->withTrashed()->first()->is_ali_express ?? "";
                    } else if ($this->order->shop->user->type == User::BusinessType) {
                        $details->id = $details->businessProductDetails->id;
                        $details->title = $details->businessProductDetails->name ?? null;
                        $details->image = $details->businessProductDetails->mainImagePath ?? null;
                        $details->brand = $details->businessProductDetails->brand;
                        $details->is_ali_express = $details->businessProductDetails->is_ali_express;
                    }


                    return collect($details)->map(function ($value, $key) {
                        return ($key == 'size_detail' || $key == 'color_detail') ? $value : (is_null($value) ? '' : $value);
                    })
                        ->only('id', 'title', 'quantity', 'amount', 'image', 'business_id', 'color_detail', 'size_detail', 'brand', 'is_ali_express')
                        ->all();
                }
            }),
            'delivery_instructions' => $this->order->delivery_instructions,
        ];
        return $data;
    }

    private function getTransactions($transactions)
    {
        return $transactions->map(function ($transaction) {
            $transaction->gatewayName = $transaction->gatewayName;
            $transaction->transaction_id = $transaction->transaction_id ?? '--';
            $transaction->transactionStatus = $transaction->transactionStatus['title'];
            return collect($transaction)->only('id', 'gatewayName', 'transaction_id', 'transactionStatus')
                ->all();
        });
    }
}

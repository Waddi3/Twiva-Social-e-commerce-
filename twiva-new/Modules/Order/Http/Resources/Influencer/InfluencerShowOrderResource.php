<?php

namespace Modules\Order\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Order\Respositories\Business\OrderRepository;
use Modules\Order\Respositories\Influencer\InfluencerOrderRepository;
use Modules\User\Entities\User;

class InfluencerShowOrderResource extends ResourceCollection
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
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->id,
            'status' => $this->order->statusString,
            'buyer_email' => $this->order->shippingAddress->email ?? '-',
            'shipping' => $this->order->shipping_amount,
            'sub_total' => InfluencerOrderRepository::calculateInfluencerAmountFromOrderWithShipping($this->order),
            'order_date' => $this->order->created_at->format('d M, Y'), // , H:i a
            'shipping_address' => $this->getShippingAddressDetails($this->order->shippingAddress),
            'paymentMethod' => $this->order->latestTransaction[0]?->gatewayName ?? "",
            'paymentStatus' => $this->order->latestTransaction[0]?->transactionStatus['title'] ?? "",
            'products' => $this->order->orderDetails->map(function ($details) {
                // $details === OrderDetails
                if ($details->productDetails) {
                    $details->amount = $details->InfluencerAmount;
                    if ($this->order->shop->user->type == User::InfluencerType) {
                        $details->id = $details->productDetails->actualDetail?->id;
                        $details->title = $details->productDetails->actualDetail?->name ?? null;
                        $details->image = $details->productDetails->productMainImage?->imageUrl ?? null;
                        $details->brand = $details->productDetails->product?->brand;
                        $details->is_ali_express = $details->productDetails->product?->is_ali_express;
                    } else if ($this->order->shop->user->type == User::BusinessType) {
                        $details->id = $details->businessProductDetails->id;
                        $details->title = $details->businessProductDetails->name ?? null;
                        $details->image = $details->businessProductDetails->mainImagePath ?? null;
                        $details->brand = $details->businessProductDetails->brand;
                        $details->is_ali_express = $details->businessProductDetails->is_ali_express;
                    }

                    return collect($details)
                        ->only('id', 'title', 'quantity', 'amount', 'image', 'business_id', 'size', 'color', 'product_price', 'product_retail_price', 'product_discount_price', 'color_detailes', 'size_detailes', 'brand', 'is_ali_express')
                        ->all();
                }
            }),
            'delivery_instructions' => $this->order->delivery_instructions,
        ];
    }
}

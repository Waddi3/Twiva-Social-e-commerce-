<?php

namespace Modules\Partner\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Entities\Order;
use Modules\Order\Respositories\Business\OrderRepository;
use Modules\User\Entities\User;

class PartnerShowOrderResource extends JsonResource
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
            'order_date' => $this->order->created_at->format('d M, Y'), // , H:i a
            'shipping_address' => $this->getShippingAddressDetails($this->order->shippingAddress),
            'payment_status' => ucfirst(strtolower(Order::PAYMENT_STATUS[$this->order->payment_status])),
            'paymentStatus' => $this->order->latestTransaction[0]?->transactionStatus['title'] ?? "",
            'business' => $this->order->businesses->map(function ($business) {
                if ($business) {
                    $business->phone_number = $business->phone_code . $business->phone_number;
                    $business->address = $business->businessDetails;

                    return collect($business)->only('id', 'name', 'email', 'phone_number', 'status', 'description',
                        'quantity', 'products', 'profile_image', 'address')
                        ->all();
                }
                return "";
            }),
            'delivery_instructions' => $this->order->delivery_instructions,
            'shipping' => $this->order->shipping_amount,
            'sub_total' => $this->order->order_amount, // OrderRepository::calculateBusinessAmountFromOrder($this->order),
            'tax' => 0,
            'total' => $this->order->order_amount + $this->order->shipping_amount, // $this->order->BusinessAmount,
        ];
        return $data;
    }
}

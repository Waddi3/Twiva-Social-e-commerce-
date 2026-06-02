<?php

namespace Modules\Notification\Mail;

use Illuminate\Mail\Mailable;
use Modules\Order\Entities\Order;

class BuyerOrderDetailsMail extends Mailable
{
    public $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function build()
    {
        $buyerName = $this->orders[0]?->buyer->name ?? $this->orders[0]->shippingAddress->contact_name;
        return $this->markdown('notification::emails.buyer-order-details', ['orders' => $this->orders, 'buyerName' => $buyerName]);
    }
}
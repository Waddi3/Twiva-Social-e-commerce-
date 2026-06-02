<?php

namespace Modules\Order\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaidSuccessfully implements ShouldQueue
{
    use SerializesModels, Dispatchable, Queueable;

    public $order;

    public function __construct($orderID)
    {
        $this->order = $orderID;
    }

}

<?php

namespace Modules\Order\Respositories\Business;

class OrderRepository
{
    public static function calculateBusinessAmountFromOrder($order)
    {
        $sum = 0;
        foreach ($order->orderDetails as $detail) {
            $sum += $detail->businessAmount;
        }
        return $sum;
    }

    public static function calculateBusinessAmountFromOrderWithShipping($order)
    {
        $sum = 0;
        foreach ($order->orderDetails as $detail) {
            $sum += $detail->businessAmount;
        }
        return $sum + $order->shipping_amount;
    }


}

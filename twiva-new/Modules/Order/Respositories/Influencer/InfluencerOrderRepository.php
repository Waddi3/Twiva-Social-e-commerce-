<?php

namespace Modules\Order\Respositories\Influencer;

class InfluencerOrderRepository
{
    public static function calculateInfluencerAmountFromOrderWithShipping($order)
    {
        return round($order->InfluencerAmount);
    }


}

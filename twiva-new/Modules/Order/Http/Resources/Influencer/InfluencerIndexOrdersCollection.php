<?php

namespace Modules\Order\Http\Resources\Influencer;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Respositories\Influencer\InfluencerOrderRepository;

class InfluencerIndexOrdersCollection extends JsonResource
{
    public function __construct($orders)
    {
        parent::__construct($orders);
    }

    public function toArray($request)
    {
//        dd($this);
        return [
            'id' => $this->id,
            'amount' => InfluencerOrderRepository::calculateInfluencerAmountFromOrderWithShipping($this),
            'status' => $this->statusString,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}

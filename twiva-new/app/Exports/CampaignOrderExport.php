<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;

class CampaignOrderExport implements FromView
{
    public function view(): View
    {
        return view('exports.campaign-order', [
            'sales' => OrderDetails::where('product_id', Order::CampaignProductIDFromInfluencerShop)->with('Order', 'Order.shippingAddress', 'productFromInfluencerShop')->get()
        ]);
    }
}
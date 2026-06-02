<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Modules\Order\Entities\Order;

class SplitOrdersCommand extends Command
{
    protected $signature = 'orders:split';

    protected $description = 'split orders details ';

    public function handle()
    {
        $orders = Order::withTrashed()->with(['orderDetails'])->get();
        $count = 0;
        foreach ($orders as $order)
        {
            // check if order need to be split
            //
            if ($this->hasMoreHasOneBusiness($order))
            {
                $this->output->warning("{$order->id} should be spited.");
                $productOrdersByBusiness = $order->orderDetails->groupBy('business_id');
                $i = 0;
                foreach ($productOrdersByBusiness as  $businessOrderProducts)
                {
//                    dd($productOrdersByBusiness);
                    $sum = 0;
                    if (!$i)
                    {
                        // update current order to match the current OrderDetails

                    }
                    else
                    {
                        // create new order with the other product
//                        assign order details to it
                    }
                    $i++;
                }

                $count++;
                $this->output->info($order->id);
            }
        }
        dd($count);

    }


    private function hasMoreHasOneBusiness($order)
    {
        return count($order->orderDetails->groupBy('business_id')) > 1;
    }
}

<?php

namespace Modules\Order\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Home\Entities\Transaction;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;

class FixOrdersTableCommand extends Command
{
    protected $signature = 'orders:fix-table';

    protected $description = 'Command description';

    public function handle()
    {
        $this->call('migrate', [
            '--path' => 'Modules/Order/Database/Migrations/2022_11_02_222253_add_business_id_and_shipping_amount_to_orders_table.php',
            '--force' => 'true'
        ]);
        $this->call('migrate', [
            '--path' => 'Modules/Shop/Database/Migrations/2022_11_06_105203_edit_orders_1_table.php'
            , '--force' => 'true'

        ]);
        foreach (Order::withTrashed()->get() as $order)
        {
            if ($order->influencerInfo?->shop)
            {
                $order->update(['influencer_id' => $order->influencerInfo->shop->id]);
            }
            else
            {
                $this->output->warning("this order has no influencer $order->id");
            }
        }
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/Shop/Database/Migrations/2022_11_06_105203_edit_orders_2_table.php'
        ]);

        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/Order/Database/Migrations/2022_11_06_175622_edit_request_id_transactions_table.php'
        ]);

        foreach (Order::withTrashed()->get() as $order)
        {
            if ($order->orderDetails->count())
            {
                $order->update(['business_id' => $order->orderDetails[0]->business_id]);
                $order->orderDetails->groupBy('business_id')->first();
                foreach ($order->orderDetails->groupBy('business_id')->first() as $detail)
                {
                    $detail->update(['business_id' => null]);
                }
            }
            else
            {
                $order->forceDelete();
            }
        }
        $ordersDetails = OrderDetails::withTrashed()->whereNotNull('business_id')->get();
        foreach ($ordersDetails as $ordersDetail)
        {
            $this->output->info("{$ordersDetail} will be split");
            $mainOrder = $ordersDetail->Order;
            $newOrder = Order::create([
                'buyer_id' => $mainOrder->buyer_id,
                'shop_id' => $mainOrder->store_id,
                'business_id' => $ordersDetail->business_id,
                'address_id' => $mainOrder->address_id,
                'order_status' => $mainOrder->order_status,
                'order_amount' => $ordersDetail->quantity * $ordersDetail->amount,
                'shipping_amount' => $mainOrder->shipping_amount,
                'payment_status' => $mainOrder->payment_status,
                'delivery_date' => $mainOrder->delivery_date,
                'created_at' => $ordersDetail->created_at,
                'updated_at' => $ordersDetail->updated_at,
                'deleted_at' => $mainOrder->deleted_at,
            ]);
            $ordersDetail->update(['order_id' => $newOrder->id, 'business_id' => null]);
        }
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/Order/Database/Migrations/2022_11_06_221518_orders_transactions_table.php'
        ]);
        $orderTransactions = Transaction::where('request_type', 2)->get();
        foreach ($orderTransactions as $orderTransaction)
        {
            if (Order::withTrashed()->whereId($orderTransaction->request_id)->exists())
            {
                DB::table('orders_transactions')
                    ->insert(
                        [
                            'order_id' => $orderTransaction->request_id,
                            'transaction_id' => $orderTransaction->id,
                        ]);
            }

        }
    }
}

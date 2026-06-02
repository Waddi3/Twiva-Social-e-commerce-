<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;

class UpdatePrivilegeUserByOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:privileges-by-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders users and convert it to privilege.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $twoDaysAgo = $now->subDays(2);

        Order::with([
            'orderDetails',
            'shop:id,user_id,shop_name,is_active,domain',
            'shop.user:id,name,email,type,phone_number,phone_code,status,is_verified,is_profile_completed,waive_prompt,privilege'
        ])
            ->where('created_at', '>=', $twoDaysAgo)
            ->where('e_commerce', '=', 0)
            ->where('order_status', Order::DELIVERED_STATUS)
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    if ($order->shop->user->type == User::InfluencerType) {
                        $profit = $order->influencerAmount;
                        $privileges = $profit / 5;
                        if ($profit) {
                            $order->shop->user->increment('privilege', $privileges);
                        }
                    }elseif ($order->shop->user->type == User::BusinessType) {
                        $profit = $order->BusinessAmount;
                        $privileges = $profit / 5;
                        if ($profit) {
                            $order->shop->user->increment('privilege', $privileges);
                        }
                    }
                }
            });
    }
}

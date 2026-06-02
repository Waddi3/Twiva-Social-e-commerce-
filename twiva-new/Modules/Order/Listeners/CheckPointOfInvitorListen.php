<?php

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Order\Events\DeliveredOrderEvent;
use Modules\User\Entities\User;
use Modules\User\Entities\UserReferralCode;
use Modules\User\Helper\EarningUser;

class CheckPointOfInvitorListen implements ShouldQueue
{

    use InteractsWithQueue;

    //    public $queue = 'test';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param DeliveredOrderEvent $event
     * @return void
     */
    public function handle($event)
    {
        $order = $event->order;

        //        $business = $order->business;
        $shop = $order->shop;
        $shop_user = $order->shop->user;

        $invited = UserReferralCode::with('invitor')
            ->where('invited_id', $shop_user?->id)
            ->whereNull('checked_at')
            ->first();


        if ($invited) {
            $points = 0;

            if ($invited->invited_type == User::BusinessType) {
                $earning_amount = EarningUser::calculateBusinessEarning($shop_user, $shop);
                if ($earning_amount > 50000) {
                    $points = 4;
                }
            } elseif ($invited->invited_type == User::InfluencerType) {
                $earning_amount = EarningUser::calculateEarning($shop_user, $shop);
                if ($earning_amount > 10000) {
                    $points = 1;
                }
            }
            if ($points > 0) {
                $invited->invitor->increment('points', $points);
                $invited->update([
                    'checked_at' => now(),
                ]);
            }
        }
    }
}

<?php

namespace Modules\Payment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Order\Entities\Order;
use Modules\Payment\Entities\WinningPayout;
use Modules\User\Entities\User;
use Modules\User\Helper\EarningUser;

class WithdrawForMasokoJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $masoko_user = User::with('mpesa')
            ->where('email', env('TWIVA_MASOKO_EMAIL', 'nwainaina@safaricom.co.ke'))
            ->first();

        if ($masoko_user)
        {
            if ($this->order->business_id == $masoko_user->id)
            {
                if ($this->order->order_status >= Order::PROCESSING_STATUS)
                {
                    $have_init_request = WinningPayout::where('user_id', $masoko_user->id)
                        ->where(function (Builder $statusQuery)
                        {
                            $statusQuery->where('status', WinningPayout::STATUS_INIT)
                                ->orWhere('status', WinningPayout::STATUS_IN_PROGRESS);
                        })->exists();
                    if ($have_init_request)
                    {
                        return;
                    }
                    else
                    {
                        $orders_earnings = EarningUser::calculateBusinessEarning($masoko_user);
                        list($transaction_amount, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($masoko_user, $orders_earnings, 0);
                        $winning_payout = WinningPayout::create([
                            'user_id'            => $masoko_user->id,
                            'till_number'        => $masoko_user->mpesa->paybill,
                            'transaction_amount' => $transaction_amount,
                        ]);
                    }
                }
            }
        }
    }
}

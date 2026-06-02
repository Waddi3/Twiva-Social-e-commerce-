<?php

namespace Modules\Payment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Notification\Notifications\Influencer\WinningPayoutNotification;
use Modules\Payment\Entities\WinningPayout;
use Modules\Payment\Services\PayoutPaymentService;

class PayoutOrdersPaymentJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $winning_payout; // WinningPayout

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($winning_payout = null)
    {
        $this->winning_payout = $winning_payout;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->winning_payout)
        {
            $this->fetchWinningPayout();
        }
        $this->payoutPaymentHandle();
    }

    private function fetchWinningPayout()
    {
        $this->winning_payout = WinningPayout::where('status', WinningPayout::STATUS_INIT)
            ->whereNull('transaction_response')
            ->first();
    }

    private function payoutPaymentHandle()
    {
        if ($this->winning_payout)
        {
            $this->winning_payout->update([
                'status' => WinningPayout::STATUS_IN_PROGRESS
            ]);
            $response = PayoutPaymentService::processPayoutPayment($this->winning_payout);
            $response_body = json_decode($response->body());
            if (isset($response_body->errorCode) && $response_body->errorCode)
            {
                $this->winning_payout->update([
                    'status' => WinningPayout::STATUS_FAIL,
                ]);
                // notify user and admin
                $user = $this->winning_payout->user;
                $user->notify(new WinningPayoutNotification($this->winning_payout));
            }
        }
    }
}

<?php

namespace Modules\Payment\Console\Commands;

use Illuminate\Console\Command;
use Modules\Notification\Notifications\Influencer\WinningPayoutNotification;
use Modules\Payment\Entities\WinningPayout;
use Modules\Payment\Services\PayoutPaymentService;

class PayOutFirstMpesaPaymentRequestCommand extends Command {
    protected $signature = 'payout:first-mpesa-payment-request';

    protected $description = 'Command description';

    public function handle(): void
    {
        $winningPayout = $this->getFirstPayoutRequest();
        if ($winningPayout)
        {
            $winningPayout->update([
                'status' => WinningPayout::STATUS_IN_PROGRESS
            ]);
            $response = PayoutPaymentService::processPayoutPayment($winningPayout);
            $response_body = json_decode($response->body());
            if (isset($response_body->errorCode) && $response_body->errorCode)
            {
                $winningPayout->update([
                    'status' => WinningPayout::STATUS_FAIL,
                ]);
//                 notify user and admin
                $user = $winningPayout->user;
                $user->notify(new WinningPayoutNotification($winningPayout));
            }
        }
    }

    private function getFirstPayoutRequest(): ?WinningPayout
    {
        return WinningPayout::where('status', WinningPayout::STATUS_INIT)
            ->whereNull('transaction_response')
            ->first() ?? null;
    }
}

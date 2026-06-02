<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Home\Entities\Transaction;


class CancelTransactionsAfterTimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    public function handle()
    {
        $transactions = Transaction::where('status', '=', Transaction::WaitingForPayment)
            ->where('created_at', '<', (Carbon::now()->subMinutes(2)->toDateTimeString()))
            ->where('payment_gateway_type', Transaction::MPESAGateAway)
            ->update(['status' => Transaction::TransactionFailed]);
    }
}
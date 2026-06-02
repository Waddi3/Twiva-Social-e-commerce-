<?php

namespace Modules\Payment\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Campaign\Entities\PostUser;
use Modules\Home\Entities\Transaction;

class PayInfluencerCampaignEarningsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const PAY_AFTER_DAYS = 1;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PostUser::where('status', PostUser::HiredStatus)
            // ->whereDoesntHave('post.transactions')
            ->where('is_payable', 0)
//            ->where('created_at', '<=', Carbon::now()->subDays(self::PAY_AFTER_DAYS))
            ->update(['is_payable' => 1]);
    }
}

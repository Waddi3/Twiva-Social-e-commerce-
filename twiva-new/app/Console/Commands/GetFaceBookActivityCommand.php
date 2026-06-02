<?php

namespace App\Console\Commands;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Home\Entities\Transaction;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Services\FacebookServices;
use Modules\User\Entities\User;


class GetFaceBookActivityCommand extends Command
{
    protected $signature = 'facebook:get-activities';
    protected $description = 'Command description';

    public function handle()
    {
        // Log::debug('GetFaceBookActivityJob');
        $users = User::with(['UserPlatforms' => function ($UserPlatformsQuery) {
            $UserPlatformsQuery->where('platform_id', Platform::FacebookID);
        }])->whereHas('UserPlatforms', function ($UserPlatformsQuery) {
            $UserPlatformsQuery
                ->where('platform_id', Platform::FacebookID)
                ->where('token', '!=', null)
                ->where('additional_info', '!=', null);
        })->get();
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        foreach ($users as $user)
        {
            FacebookServices::getFaceBookActivities($user);
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}

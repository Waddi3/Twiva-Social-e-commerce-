<?php

namespace App\Console;

use App\Jobs\CancelTransactionsAfterTimeJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Payment\Jobs\PayInfluencerCampaignEarningsJob;
use Modules\Payment\Jobs\PayoutOrdersPaymentJob;
use Modules\Platform\Jobs\ScheduleSocialMediaJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (app()->environment() != 'local') {
            //payments
            $schedule->command('payout:first-mpesa-payment-request')
                ->everyMinute()
                ->withoutOverlapping();
            $schedule->job(new CancelTransactionsAfterTimeJob())->everyMinute();
            //            $schedule->command('otp:clean-table')->everyTenMinutes();
            // socials
            $schedule->command('facebook:get-activities')->dailyAt("00:00");
            $schedule->command('instagram:get-activities')->dailyAt("01:00");
            $schedule->job(new ScheduleSocialMediaJob())->everyMinute();
            //shop-able content
            //            $schedule->job(new PayInfluencerCampaignEarningsJob())->dailyAt("00:00");
            $schedule->job(new PayInfluencerCampaignEarningsJob())->everyMinute()
                ->withoutOverlapping();
            $schedule->command('videos:delete-old')->dailyAt('02:00')
                ->withoutOverlapping();
            $schedule->command('users:privileges-by-order')->days(2)->at('01:00')
                ->withoutOverlapping();
            $schedule->command('users:privileges-by-publish')->days(2)->at('01:30')
                ->withoutOverlapping();


            // aliexpress
            $schedule->command('ali-express:generate-token')->everySixHours();
            //notify
            $schedule->command('reminders:send-cart')->everyMinute();
        }
    }

    protected function commands()
    {

        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}

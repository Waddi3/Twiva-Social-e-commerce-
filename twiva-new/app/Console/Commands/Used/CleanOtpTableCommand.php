<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Modules\User\Entities\Otp;

class CleanOtpTableCommand extends Command
{
    protected $signature = 'otp:clean-table';

    protected $description = 'Command description';

    public function handle()
    {
        Otp::whereTime('expiry_date', '<', now())->delete();
    }
}

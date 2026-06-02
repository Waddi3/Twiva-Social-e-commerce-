<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Modules\Business\Entities\Company;

class FixBusinessDetails extends Command
{
    protected $signature = 'business:fix-details';

    protected $description = 'Remove Unused Tables';

    public function handle()
    {
        $this->warn('Running FixBusinessDetails.php');


        $businesses = Company::all();
        $progressBar = $this->output->createProgressBar($businesses->count());
        $progressBar->start();
        foreach ($businesses as $business)
        {
            if ($business->user)
            {
                $phoneNumber = str_replace('+254', '', $business->busi_number);
                $phoneNumber = str_replace('254-', '', $phoneNumber);
                $phoneNumber = str_replace(' ', '', $phoneNumber);
                $business->user()->update([
                    'name' => $business->busi_name,
                    'description' => $business->description,
                    'profile_image' => $business->logo,
                    'phone_number' => ($phoneNumber == '' || !$phoneNumber) ? null : $phoneNumber,
                ]);
                $business->save();

            }
            else
            {
                $this->line('');
                $this->warn('Deleting: ' . $business->id);
                $business->forceDelete();
            }

            $progressBar->advance();
        }
        $progressBar->finish();
        $this->call('migrate', [
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_03_162348_clean_business_details_table.php',
            '--force' => 'true',
        ]);
        $this->info('Succeed');

    }

}

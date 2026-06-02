<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

class FixBusinessWithoutDetailsCommand extends Command
{
    protected $signature = 'business:fix-without-details';

    protected $description = 'Fix Business Without Details ';

    public function handle()
    {
        $invalidBusinesses = User::withTrashed()->where('type', User::BusinessType)->doesntHave('businessDetails')->get();
        $progressBar = $this->output->createProgressBar($invalidBusinesses->count());
        $progressBar->start();
        $this->info("invalid Business count is {$invalidBusinesses->count()} ");
        foreach ($invalidBusinesses as $invalidBusiness)
        {
            $invalidBusiness->businessDetails()->create([
                'contact_name' => $invalidBusiness->name,
                'busi_number' => Str::replace('+', '', $invalidBusiness->mobile),
                'description' => '',
                'country' => '113',
                'state' => '160',
                'city' => '64778',
            ]);
            $this->info("businessDetails created for {$invalidBusiness->name}");
            $progressBar->advance();
        }
        $progressBar->finish();

    }
}

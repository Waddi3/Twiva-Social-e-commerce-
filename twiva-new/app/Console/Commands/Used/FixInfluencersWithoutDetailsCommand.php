<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Modules\User\Entities\User;

class FixInfluencersWithoutDetailsCommand extends Command
{
    protected $signature = 'influencers:fix-without-details';

    protected $description = 'Fix Influencers Without Details ';

    public function handle()
    {
        $invalidInfluencers = User::withTrashed()->where('type', User::InfluencerType)->doesntHave('InfluencerDetails')->get();
        $progressBar = $this->output->createProgressBar($invalidInfluencers->count());
        $progressBar->start();
        $this->info("invalid Influencers count is {$invalidInfluencers->count()} ");
        foreach ($invalidInfluencers as $invalidInfluencer)
        {
            $invalidInfluencer->InfluencerDetails()->create([
                'gender' => User::MALE,
                'dob' => '2000-01-01',
                'interests' => '411',
                'location' => 'Kenya',
            ]);
            $this->info("InfluencerDetails created for {$invalidInfluencer->name}");
            $progressBar->advance();
        }
        $progressBar->finish();

    }
}

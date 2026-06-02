<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Modules\Influencer\Entities\InfluencerDetails;

class FixInfluencersDetails extends Command
{
    protected $signature = 'influencers:fix-details';

    protected $description = 'Remove Unused Tables';

    public function handle()
    {
        $this->warn('Running FixInfluencersDetails.php');

        $influencers = InfluencerDetails::withTrashed()->get();
        $progressBar = $this->output->createProgressBar($influencers->count());
        $progressBar->start();
        foreach ($influencers as $influencer)
        {
            if ($influencer->user)
            {
                $influencer->user()->update([
                    'name' => $influencer->name,
                    'description' => $influencer->description,
                    'profile_image' => $influencer->profile_image,
                ]);
                if ($influencer->gender == 1)
                {
                    $influencer->gender = 'MALE';
                }
                else if ($influencer->gender == 2)
                {
                    $influencer->gender = 'FEMALE';
                }
                else
                {
                    $influencer->gender = 'NOT_SPECIFIED';
                }
                $influencer->save();
            }
            else
            {
                $influencer->delete();
            }

            $progressBar->advance();
        }
        $progressBar->finish();
        $this->call('migrate', [
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_03_162348_clean_influencer_details_table.php',
            '--force' => 'true'
        ]);
        $this->call('migrate', [
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_19_075735_edit_story_post_table.php',
            '--force' => 'true'
        ]);
    }

}

<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;

class CleanInfluencerShopTable extends Command
{
    protected $signature = 'influencer-shop-table:clean';

    protected $description = 'Clean Influencer Shop Table';

    public function handle()
    {
        $this->warn('Running CleanInfluencerShopTable');
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_04_153009_drop_colors_from_influencer_shop_table.php'
        ]);
        $this->info('CleanInfluencerShopTable Succeed');

    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Order\Entities\OrderDetails;

class BusinessStoreFeatureCommand extends Command
{
    protected $signature = 'business:store-feature';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('running shops:rename-tables ');
        $this->call('shops:rename-tables');
        $this->info('running business:create-shops:');
        $this->call('business:create-shops');



    }
}

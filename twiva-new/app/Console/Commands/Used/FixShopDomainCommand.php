<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Shop\Entities\Shops;

class FixShopDomainCommand extends Command
{
    protected $signature = 'shop:fix-domain';

    protected $description = 'shop:fix-domain';

    public function handle()
    {
        $shops = Shops::select('id', 'domain')->where('domain', 'like', '%\_%')->get();
        $progressBar = $this->output->createProgressBar($shops->count());
        $progressBar->start();
        $count = 0;
        foreach ($shops as $shop)
        {
            $newDomain = Str::replace('_', '-', $shop->domain);
            if (Shops::where('domain', $newDomain)->exists())
            {
                $newDomain = $newDomain . '1';
                $count++;
                $this->info("domain exists $newDomain count is $count");
                $this->line('');
            }
            $shop->update(['domain' => $newDomain]);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->line('');


    }
}

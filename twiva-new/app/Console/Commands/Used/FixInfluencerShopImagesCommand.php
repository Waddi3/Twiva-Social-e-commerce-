<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Modules\Shop\Entities\Shops;

class FixInfluencerShopImagesCommand extends Command
{
    protected $signature = 'influencer:fix-shop-images';

    protected $description = 'Command description';

    public function handle()
    {
        $shops = Shops::where('shop_logo_path', 'like', "%api.%")->get();
        $progressBar = $this->output->createProgressBar($shops->count());
        $progressBar->start();
        foreach ($shops as $shop)
        {
            $progressBar->advance();
            $shop->update(['shop_logo_path' => str_replace('https://api.twiva.co.ke/storage/images/products/', '', $shop->shop_logo_path)]);
        }
        $progressBar->finish();
        $this->output->newLine();
    }
}

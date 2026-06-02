<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\User;

class CreateBusinessShopsCommand extends Command
{
    protected $signature = 'business:create-shops';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::withTrashed()
            ->where('type', User::BusinessType)
            ->with('shop', 'businessDetails')
            ->get();
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        foreach ($users as $user)
        {
            if ($user->shop)
            {
                $user->shop->forceDelete();
            }
            $shop = new Shops();
            $shop->user_id = $user->id;
            $shop->shop_name = $user->name ?? ($user->businessDetails->contact_name ?? '--');
            $shop->shop_logo_path = str_replace([asset('storage/images/products/'), asset('storage/images/products/') . '/'], '', $user->profile_image);
            $shop->shop_cover_image_path = '1655893364.jpeg';
            $shop->is_active = 1;
            $shop->domain = $shop->generateDomainFromShopName($user->name, true);
            $shop->save();
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->output->newLine();
    }
}

<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\SubCategories;
use Modules\Platform\Entities\Platform;

class ProductionCommandsOrder extends Command
{
    protected $signature = 'db:update';

    protected $description = 'Production Database Edits';

    public function handle()
    {
        Category::create(['name' => "Others"]);
        Category::create(['name' => "Services"]);
        SubCategories::find(112)->update(['name' => 'Mattresses']);
        Platform::find(Platform::FacebookID)->update(['icon' => 'shop/images/social-icons/fb.svg']);
        Platform::find(Platform::InstagramID)->update(['icon' => 'shop/images/social-icons/instagram.svg']);
        Platform::find(Platform::TwitterID)->update(['icon' => 'shop/images/social-icons/twitter.svg']);
        Platform::find(Platform::YoutubeID)->update(['icon' => 'shop/images/social-icons/youtube.svg']);
        DB::statement("UPDATE `comp_details` SET `country`='113' WHERE country is null ;");
        DB::statement("UPDATE `comp_details` SET `state`='158' WHERE state is null ;");
        DB::statement("UPDATE `comp_details` SET `city`='64778' WHERE city is null ;");


        $default = config('app.debug');
        if ($this->confirm('Do you want to run  migrate:ordered ?', $default))
        {
            $this->call('migrate:ordered');
        }
//
        if ($this->confirm('Do you want to run users-table:first-clean ?', $default))
        {
            $this->call('users-table:first-clean');
        }

        if ($this->confirm('Do you want to run fix:sizes ?', $default))
        {
            $this->call('fix:sizes');
        }

        if ($this->confirm('Do you want to run tables:remove-unused ?', $default))
        {
            $this->call('tables:remove-unused');
        }
        if ($this->confirm('Do you want to run tables:rename ?', $default))
        {
            $this->call('tables:rename');
        }
        if ($this->confirm('Do you want to run users:fix-details? ?', $default))
        {
            $this->call('users:fix-details');
        }

        if ($this->confirm('Do you want to run influencers:fix-details? ?', $default))
        {
            $this->call('influencers:fix-details');
        }

        if ($this->confirm('Do you want to run business:fix-details ?', $default))
        {
            $this->call('business:fix-details');
        }
        if ($this->confirm('Do you want to run influencer-shop-table:clean ?', $default))
        {
            $this->call('influencer-shop-table:clean');
        }

        if ($this->confirm('Do you want to run fix:buyers ?', $default))
        {
            $this->call('fix:buyers');
        }
//        if ($this->confirm('Do you want to run categories:remove-duplication ?', $default))
//        {
//            $this->call('categories:remove-duplication');
//        }

    }


}

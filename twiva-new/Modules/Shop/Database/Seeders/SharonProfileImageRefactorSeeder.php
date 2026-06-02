<?php

namespace Modules\Shop\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Shop\Entities\Shops;

class SharonProfileImageRefactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $shop = Shops::where('domain', 'sharon-nzilani')
            ->first();

        if( !$shop ) {
            $this->command->error('User not found');
            return;
        }

        $shop->shop_logo_path = '';
        $shop->save();

        $this->command->info('The address of shop image fixed');
    }
}

<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\System\Entities\MobileVersion;

class SeedMobileVersionTableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        MobileVersion::create([
            'app_name' => MobileVersion::ANDROID_PLATFORM,
            'platform' => MobileVersion::ANDROID_PLATFORM,
            'version' => '0.0.1',
            'store_link' => 'https://play.google.com/store/apps',
        ]);

        MobileVersion::create([
            'app_name' => MobileVersion::IOS_PLATFORM,
            'platform' => MobileVersion::IOS_PLATFORM,
            'version' => '0.0.1',
            'store_link' => 'https://www.apple.com/store',
        ]);
    }
}

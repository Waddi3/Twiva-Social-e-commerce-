<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Entities\Platform;

class PlatformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Platform::create(['name' => 'Facebook', 'icon' => 'assets/svg/fb.svg']);
        Platform::create(['name' => 'Instagram', 'icon' => 'assets/svg/instagram.svg']);
        Platform::create(['name' => 'Twitter', 'icon' => 'assets/svg/twitter.svg']);
        Platform::create(['name' => 'Youtube', 'icon' => 'assets/svg/youtube.svg']);
        Platform::create(['name' => 'TikTok', 'icon' => 'assets/svg/tiktok.svg']);
        Platform::create(['name' => 'Whatsapp', 'icon' => 'assets/svg/whatsapp.svg']);
    }
}

<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Entities\Platform;

class ChangeTwitterToXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $platform = Platform::where('name', 'Twitter')->first();
        if( !$platform ) {
            $this->command->error('Twitter platform not found');
            return;
        }

        $platform->update([
            'name' => 'X',
            'icon' => 'shop/images/social-icons/x.svg',
            'link' => 'https://www.x.com/'
        ]);

        $this->command->info('Twitter platform information has updated');
    }
}

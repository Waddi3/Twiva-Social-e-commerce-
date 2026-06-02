<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Entities\ReasonReject;

class ReasonRejectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tmp = ReasonReject::insert([
            [
                'reason' => 'There aren\'t enough people in the group.',
                'order' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'The group isn\'t active or engaging',
                'order' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'The group name isn\'t good.',
                'order' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        dd($tmp);
    }
}

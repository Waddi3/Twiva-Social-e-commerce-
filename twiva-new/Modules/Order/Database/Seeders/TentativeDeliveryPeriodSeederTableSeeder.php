<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Entities\TentativeDeliveryPeriod;

class TentativeDeliveryPeriodSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $period_days = [
            [
                'days' => 0,
            ],
            [
                'days' => 1,
            ],
            [
                'days' => 2,
            ],
            [
                'days' => 3,
            ],
            [
                'days' => 4,
            ],
            [
                'days' => 5,
            ],
            [
                'days' => 6,
            ],
            [
                'days' => 7,
            ],
            [
                'days' => 8,
            ],
            [
                'days' => 9,
            ],
            [
                'days' => 10,
            ],
            [
                'days' => 11,
            ],
            [
                'days' => 12,
            ],
            [
                'days' => 13,
            ],
            [
                'days' => 14,
            ],
            [
                'days' => 15,
            ],
            [
                'days' => 16,
            ],
            [
                'days' => 17,
            ],
            [
                'days' => 18,
            ],
            [
                'days' => 19,
            ],
            [
                'days' => 20,
            ],
            [
                'days' => 21,
            ],
            [
                'days' => 22,
            ],
            [
                'days' => 23,
            ],
            [
                'days' => 24,
            ],
            [
                'days' => 25,
            ],
            [
                'days' => 26,
            ],
            [
                'days' => 27,
            ],
            [
                'days' => 28,
            ],
            [
                'days' => 29,
            ],
            [
                'days' => 30,
            ],

        ];
        foreach ($period_days as $period_day) {
            TentativeDeliveryPeriod::create($period_day);
        }
        // $this->call("OthersTableSeeder");
    }
}

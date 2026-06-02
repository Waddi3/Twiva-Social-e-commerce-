<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Badge;

class BadgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // php artisan module:seed User --class=BadgesTableSeeder
        // Badge::truncate();

        $badges = [
            [
                'title' => 'Decathlon',
                'background_color' => '#353fb7',
                'color' => '#eff0f9',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'RAMTONS',
                'background_color' => '#ffeced',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'HISENSE',
                'background_color' => '#ebf5ed',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'BEKO',
                'background_color' => '#353fb7',
                'color' => '#eff0f9',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Safaricom',
                'background_color' => '#ebf5ed',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'CANON',
                'background_color' => '#ffeced',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Sollatek',
                'background_color' => '#353fb7',
                'color' => '#eff0f9',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Xiaomi',
                'background_color' => '#ff6700',
                'color' => '#fff3ed',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'City Walk',
                'background_color' => '#ffff00',
                'color' => '#ffffb0',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Vitron',
                'background_color' => '#ffeced',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Vivo Phones',
                'background_color' => '#353fb7',
                'color' => '#eff0f9',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Vivo Fashion',
                'background_color' => '#ff6700',
                'color' => '#fff3ed',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
            [
                'title' => 'Phone Place',
                'background_color' => '#ebf5ed',
                'color' => '#027A48',
                'created_at' =>now(),
                'updated_at' =>now(),
            ],
        ];

        Badge::insert($badges);
    }
}

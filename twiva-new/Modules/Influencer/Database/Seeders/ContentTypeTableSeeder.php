<?php

namespace Modules\Influencer\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Influencer\Entities\ContentType;

class ContentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        ContentType::create(
            [
                'name' => 'Challenges',
                'order' => 5,
            ]);
        ContentType::create(
            [
                'name' => 'Live',
                'order' => 6,
            ]);
        ContentType::create(
            [
                'name' => 'Posts',
                'order' => 2,
            ]);
        ContentType::create(
            [
                'name' => 'Reels',
                'order' => 4,
            ]);
        ContentType::create(
            [
                'name' => 'Stories',
                'order' => 3,
            ]);
        ContentType::create(
            [
                'name' => 'Videos',
                'order' => 1,
            ]);
    }
}

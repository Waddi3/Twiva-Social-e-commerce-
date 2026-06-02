<?php

namespace Modules\Influencer\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class InfluencerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
//        $this->call(SocialCategoriesTableSeeder::class);
        $this->call(ContentTypeTableSeeder::class);
        // $this->call("OthersTableSeeder");
    }
}

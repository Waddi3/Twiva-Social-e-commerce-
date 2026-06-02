<?php

namespace Modules\Influencer\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Influencer\Entities\SocialCategory;

class SocialCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        SocialCategory::create(['name' => 'Pharmacy / Drugstore']);
        SocialCategory::create(['name' => 'DJ']);
        SocialCategory::create(['name' => 'Men\'s clothing store']);
        SocialCategory::create(['name' => 'Women\'s clothing store']);
        SocialCategory::create(['name' => 'Baby & children\'s clothing store']);
        SocialCategory::create(['name' => 'Car dealership']);
        SocialCategory::create(['name' => 'Home decor']);
        SocialCategory::create(['name' => 'Petting Zoo']);
        SocialCategory::create(['name' => 'Women\'s Health Clinic']);
        SocialCategory::create(['name' => 'E-Cigarette Store']);
        SocialCategory::create(['name' => 'TV/Movie Award']);
        SocialCategory::create(['name' => 'In-Home Service']);
        SocialCategory::create(['name' => 'TV channel']);
        SocialCategory::create(['name' => 'TV show']);
        SocialCategory::create(['name' => 'TV channel']);
        SocialCategory::create(['name' => 'TV Movie Award']);
        SocialCategory::create(['name' => 'TV Movie network']);
        SocialCategory::create(['name' => 'TV Movie Season']);
        SocialCategory::create(['name' => 'RV Park']);
        SocialCategory::create(['name' => 'RV Rental']);
        SocialCategory::create(['name' => 'RV Repair Shop']);
        SocialCategory::create(['name' => 'Bed and Breakfast']);
        SocialCategory::create(['name' => 'Web Designer']);
        SocialCategory::create(['name' => 'Baby goods/kids goods']);
        SocialCategory::create(['name' => 'Restaurant']);
        SocialCategory::create(['name' => 'Real Estate']);
        SocialCategory::create(['name' => 'City Hall']);
        SocialCategory::create(['name' => 'Fan Page']);
        SocialCategory::create(['name' => 'Non-Governmental Organization (NGO)']);
        SocialCategory::create(['name' => 'Pizza place']);
        SocialCategory::create(['name' => 'Gym/Physical Fitness Center']);
        SocialCategory::create(['name' => 'Beauty, cosmetic & personal care']);
        SocialCategory::create(['name' => 'Sport league']);
        SocialCategory::create(['name' => 'Record Label']);
        SocialCategory::create(['name' => 'Bags/Luggage']);
        SocialCategory::create(['name' => 'Medical Lab']);
        SocialCategory::create(['name' => 'Teens & Kids Website']);

    }
}

<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Platform\Entities\WhatsAppCategory;

class WhatsAppCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // php artisan module:seed Platform --class=WhatsAppCategoriesTableSeeder
        Model::unguard();

        WhatsAppCategory::truncate();

        /**
         * Women
         * Youth
         * Religious
         * Estate
         * University/College
         * Teachers
         * Social
         * Politics
         * Business
         * Betting/Gaming/Sports
         * Parents
         * Alumni
         * Health/Fitness
         * Travel/Adventure
         * Professional Networking
         * Hobby/Interest
         * Support
         * Learning/Education
         * Job Seekers
         * Charity/Volunteer
         * Freelancers/Entrepreneurs
         * Diaspora
         */
        $whats_app_categories_list = [
            [
                'name' => 'Women',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Youth',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Religious',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Estate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'University/College',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teachers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Social',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Politics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Betting/Gaming/Sports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Parents',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alumni',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Health/Fitness',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Travel/Adventure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional Networking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hobby/Interest',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Learning/Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Job Seekers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Charity/Volunteer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Freelancers/Entrepreneurs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diaspora',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        WhatsAppCategory::insert($whats_app_categories_list);
    }
}

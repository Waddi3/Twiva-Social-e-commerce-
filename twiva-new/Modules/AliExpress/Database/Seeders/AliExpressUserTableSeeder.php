<?php

namespace Modules\AliExpress\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class AliExpressUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan module:seed AliExpress --class=AliExpressUserTableSeeder
     * @return void
     */
    public function run()
    {
        Model::unguard();


        // businessShipping
        // shop
        // businessDetails
        // mpesa

        $ali_express_data = [
            'name' => 'Ali Express',
            'email' => config('twiva.ali-express.business-account'),
            'type' => User::BusinessType,
            'email_verified_at' => now(),
            'password' => 'password',
            'phone_number' => '712111000',
            'phone_code' => '254',
            'status' => '1',
            'description' => 'Ali Express business account',
            'is_verified' => 1,
            'is_profile_completed' => 1,
            'profile_image' => 1,
            'mobile_verified_at' => now(),
            'waive_prompt' => 0,
        ];

        $ali_express_business = User::firstOrCreate(
            ['email' => config('twiva.ali-express.business-account')],
            $ali_express_data
        );


        $business_shipping = [
            'shipping_by_partner' => 1,
            'has_shipping_fees' => 1,
            'pay_on_delivery' => 0,
        ];

        $ali_express_business->businessShipping()->firstOrCreate([], $business_shipping);

        $shop = [
            'shop_name' => 'Ali Express',
            'shop_description' => 'Ali Express shop',
            'is_active' => 1,
            'domain' => 'ali-express',
        ];

        $ali_express_business->shop()->firstOrCreate([], $shop);

        $business_details_company = [
            'contact_name' => 'Ali Express',
            'busi_number' => '712111000',
            'description' => 'Ali Express account in Twiva',
            'country' => '113', // Kenya
            'state' => '191', // Nairobi
            'city' => '64845', // Nairobi
            'area' => 'Uhuru Park',
        ];

        $ali_express_business->businessDetails()->firstOrCreate([], $business_details_company);

        // $mpesa = [];
    }
}


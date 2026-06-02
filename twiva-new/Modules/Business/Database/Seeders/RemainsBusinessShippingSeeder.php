<?php

namespace Modules\Business\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Business\Entities\BusinessShipping;
use Modules\User\Entities\User;

class RemainsBusinessShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        $businesses = User::where('type', 'Business')->whereDoesntHave("businessShipping")->get();

        foreach( $businesses as $business) {
            BusinessShipping::create([
                'business_id' => $business->id,
                'shipping_by_partner' => 1,
                'has_shipping_fees' => 0,
            ]);
        }

        $this->command->info('"Business shipping" has been created for '.$businesses->count(). ' businesses');
    }
}

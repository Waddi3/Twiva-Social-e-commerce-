<?php

namespace Modules\Business\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Business\Entities\BusinessShipping;
use Modules\User\Entities\User;

class BusinessShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        /*$partner = User::where('type', 'Partner')->first();
        if( !$partner ) {
            $this->command->error('There is no partner to assign to businesses');
            return;
        }*/

        $businesses = User::where('type', 'Business')->get();

        //$counter = 0;
        foreach( $businesses as $business) {
            $hasShippingFees = 1;
            $shippingByPartner = null;
            if( $business->products()->where('shipping_by', 'partner')->count() ) {
                $shippingByPartner = 1;
            }
            else if( $business->products()->where('has_shipping_fees', 0)->count() ) {
                $shippingByPartner = 1;
            }

            BusinessShipping::create([
                'business_id' => $business->id,
                'shipping_by_partner' => $shippingByPartner,
                'has_shipping_fees' => $hasShippingFees,
            ]);

            /*if( $counter > 10 ) {
                return;
            }
            $counter++;*/
        }

        $this->command->info('Businesses settings have moved successfully');
    }
}

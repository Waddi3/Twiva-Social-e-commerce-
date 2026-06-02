<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class DefaultPartnerForShippingRates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $partner = User::where('type', User::PartnerType)
            ->orderBy('id', 'ASC')
            ->first();

        if( !$partner ) {
            $this->command->error('No partner found');
            return;
        }

        DB::table('shipping_rates')->update(['partner_id' => $partner->id]);

        $this->command->info('Shipping rates have default partner now');
    }
}

<?php

namespace Modules\Business\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Business\Entities\Company;
use Modules\User\Entities\User;

class AddAddressForPartner extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $partner = User::where('type', 'Partner')->first();

        if( !$partner ) {
            $this->command->error('Partner not found');
            return;
        }

        Company::create([
            'user_id' => $partner->id,
            'country' => 113,
            'state' => 200,
            'city' => 64838,
            'area' => 'Mombasa area'
        ]);

        $this->command->info('Address added');
    }
}

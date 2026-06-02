<?php

namespace Modules\Partner\Database\Seeders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;

class PartnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $user = User::where('type', User::PartnerType)
            ->first();

        if( $user ) {
            $this->command->error('A partner user already is exists');
            return;
        }

        try {
            User::create([
                'name' => 'Logistics Partner',
                'email' => 'partner@twiva.co.ke',
                'password' => Hash::make('xVg2@70W'),
                'type' => User::PartnerType,
                'status' => 1,
                'is_verified' => 1
            ]);
            $this->command->info('The logistics partner has created');
        }
        catch(Exception $x) {
            $this->command->info('Problem while creating the logistics partner user');
        }
    }
}

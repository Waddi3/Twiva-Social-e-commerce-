<?php

namespace Modules\Partner\Database\Seeders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;

class SecondPartnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        try {
            User::create([
                'name' => 'Delivery Partner 2',
                'email' => 'delivery@twiva.com',
                'password' => Hash::make('zaman'),
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

<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

class AppIdSecretUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        User::chunk(200, function (Collection $users) {
            foreach ($users as $user) {
                if (is_null($user->app_id) or is_null($user->app_secret)) {
                    $app = [
                        'app_id' => $user->generateApp('id', 16),
                        'app_secret' => $user->generateApp('secret', 32),
                    ];
                    // dd($app);
                    $user->update($app);
                }
            }
        });
    }
}

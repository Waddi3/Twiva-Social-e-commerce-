<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Modules\User\Entities\User;

class FixUsersDetails extends Command
{
    protected $signature = 'users:fix-details';

    protected $description = 'Remove Unused Tables';

    public function handle()
    {
        $this->warn('Running FixUsersDetails.php');
        $users = User::withTrashed()->get();
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();
        foreach ($users as $user)
        {
            if ($user->type == 1)
            {
                $user->type = 'Business';
            }
            else if ($user->type == 2)
            {
                $user->type = 'Influencer';
            }
            else if ($user->type == 3)
            {
                $user->type = 'Buyer';
            }
            if ($user->is_verifed)
            {
                if ($user->created_at->isAfter('20-07-2022'))
                {
                    $user->mobile_verified_at = now();
                }
                else
                {
                    $user->email_verified_at = now();
                }
            }
            else
            {
                $user->mobile_verified_at = null;
                $user->email_verified_at = null;
            }

            $user->profile_image = $user->profile;
            $user->save();
            $progressBar->advance();
        }
        $progressBar->finish();

        $this->call('migrate', [
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_03_162315_clean_users_table.php',
            '--force' => 'true'
        ]);
        $this->info('Succeed');

    }


}

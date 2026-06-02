<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;

class RenameReferralColumnInUsersTable extends Command
{
    protected $signature = 'users-table:first-clean';

    protected $description = 'Rename Referral Column In Users Table';

    public function handle()
    {
        $this->warn('Running RenameReferralColumnInUsersTable');
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_03_162315_first_clean_for_users_table.php'
        ]);
        $this->info('RenameReferralColumnInUsersTable Succeed');

    }
}

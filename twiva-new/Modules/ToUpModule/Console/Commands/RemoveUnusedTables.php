<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;

class RemoveUnusedTables extends Command
{
    protected $signature = 'tables:remove-unused';

    protected $description = 'Remove Unused Tables';

    public function handle()
    {
        $this->warn('Running RemoveUnusedTables.php');

        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_02_100333_removeUnUsedTableMigration.php'
        ]);
        $this->info('Succeed');

    }

}

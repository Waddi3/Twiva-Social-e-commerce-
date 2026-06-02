<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;

class RenameTables extends Command
{
    protected $signature = 'tables:rename';

    protected $description = 'Rename Tables';

    public function handle()
    {
        $this->warn('Running RemoveUnusedTables.php');

        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_04_190511_rename_tables.php'
        ]);
        $this->info('Succeed');

    }

}

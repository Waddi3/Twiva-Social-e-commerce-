<?php

namespace App\Console\Commands\Used;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

class OrderedMigration extends Command
{
    protected $signature = 'migrate:ordered {--oauth}';

    protected $description = 'ordered migration';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->warn('Running Ordered Migration');
        $modules = $this->getModuleNames();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        $this->info("Starting migrations...");
        $bar = $this->output->createProgressBar(count($modules));
        $bar->start();
        foreach ($modules as $module)
        {
            Artisan::call("module:migrate $module --force");
            $bar->advance();
        }
        $bar->finish();

        if ($this->option('oauth'))
        {
            $this->call("passport:install");
            $this->call("migrate");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        $this->line('');
        $this->info('Ordered Migration Succeed');
        $this->call('migrate',
            [
                '--path' => 'database/migrations/2022_09_13_100457_create_jobs_table.php',
                '--force' => 'true'
            ]);

    }

    private function getModuleNames(): array
    {
        $names = [];
        foreach (Module::collections() as $appModule)
        {
            $appModule->getName() == 'ToUpModule' ? null : $names[] = $appModule->getName();
        }
        return $names;
    }
}

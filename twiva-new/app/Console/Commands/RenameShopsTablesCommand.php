<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenameShopsTablesCommand extends Command
{
    protected $signature = 'shops:rename-tables';

    protected $description = 'rename shops tables to include business';

    public function handle()
    {
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2016_06_01_000001_create_oauth_auth_codes_table']);
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2016_06_01_000002_create_oauth_access_tokens_table']);
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2016_06_01_000004_create_oauth_clients_table']);
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2016_06_01_000003_create_oauth_refresh_tokens_table']);
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2016_06_01_000005_create_oauth_personal_access_clients_table']);
        DB::table('migrations')->insert(['batch' => 0, 'migration' => '2019_12_14_000001_create_personal_access_tokens_table']);

        $this->call('migrate', [
            '--path' => 'Modules/Shop/Database/Migrations/2022_11_13_110135_refactor_shop_elements.php',
            '--force' => 'true'
        ]);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Services\FacebookServices;
use Modules\User\Entities\User;

class UpdateFaceBookTokensToPageTokensCommand extends Command
{
    protected $signature = 'facebook:update-account-tokens-to-page-tokens';

    protected $description = 'Command description';

    public function handle()
    {
        if (app()->environment() != 'production')
        {
            $users = User::with(['UserPlatforms' => function ($UserPlatformsQuery) {
                $UserPlatformsQuery->where('platform_id', Platform::FacebookID);
            }])->whereHas('UserPlatforms', function ($UserPlatformsQuery) {
                $UserPlatformsQuery
                    ->where('platform_id', Platform::FacebookID)
                    ->where('token', '!=', null)
                    ->where('additional_info', null);
            })->get();
            $progressBar = $this->output->createProgressBar($users->count());
            $progressBar->start();
            if ($users->count())
            {
                DB::statement('CREATE TABLE user_platforms_backup LIKE user_platforms;');
                DB::statement('insert into user_platforms_backup (select * from user_platforms)');
            }
            foreach ($users as $user)
            {
                $progressBar->advance();
                FacebookServices::connectToFacebook($user);
            }
            $progressBar->finish();

            UserPlatform::where('additional_info', null)
                ->where('platform_id', Platform::FacebookID)
                ->where('token', '!=', null)
                ->update(['token' => null]);
        }
    }
}

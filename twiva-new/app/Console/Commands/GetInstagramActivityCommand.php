<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Services\MetaServices;


class GetInstagramActivityCommand extends Command
{
    protected $signature = 'instagram:get-activities';
    protected $description = 'Command description';

    public function handle()
    {
        // Log::debug('GetFaceBookActivityJob');
        $instagramPlatforms = UserPlatform::where('platform_id', Platform::InstagramID)
            ->whereNotNull('token')->get();
        $progressBar = $this->output->createProgressBar($instagramPlatforms->count());
        $progressBar->start();
        foreach ($instagramPlatforms as $platform)
        {
            $platform->update(['token' => MetaServices::getLongLifeToken($platform->token)]);
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
